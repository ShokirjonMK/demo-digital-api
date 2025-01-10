<?php

namespace api\controllers;

use api\components\MipServiceMK;
use api\forms\Login;
use common\models\model\Languages;
use common\models\model\LoginHistory;
use common\models\model\SubjectCategory;
use common\models\model\TeacherAccess;
use common\models\Subject;
use Yii;
use api\resources\User;
use base\ResponseStatus;
use common\models\AuthAssignment;
use common\models\model\AuthChild;
use common\models\model\Department;
use common\models\model\Faculty;
use common\models\model\Kafedra;
use common\models\model\Oferta;
use common\models\model\Profile;
use common\models\model\UserAccess;
use yii\db\Expression;
use yii\web\UploadedFile;

class UserController extends ApiActiveController
{

    public $modelClass = 'api\resources\User';

    public function actions()
    {
        return [];
    }


    public function actionGet($pin, $document_issue_date)
    {
        $mip = MipServiceMK::getData($pin, $document_issue_date);

        if ($mip['status']) {
            return $this->response(1, _e('Success'), $mip['data']);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $mip['error'], ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionMe()
    {
        $data = null;
        $errors = [];
        $user = User::findOne(current_user_id());

        if (isset($user)) {
            if ($user->status === User::STATUS_ACTIVE) {
                $profile = $user->profile;

                $isMain = Yii::$app->request->get('is_main') ?? 1;
                if ($isMain == 0) {
                    if (!$user->getIsRoleStudent()) {
                        Login::logout();
                        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UNAUTHORIZED);
                    }
                    $data = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'last_name' => $profile->last_name,
                        'first_name' => $profile->first_name,
                        // 'role' => $user->getRoles(),
                        'profile' => $profile,
                        'edu_form' => $user->student->eduForm,
                        'role' => $user->getRolesStudent(),
                        'oferta' => $user->getOfertaIsComformed(),
                        'email' => $user->email,
                        // 'is_changed' => $user->is_changed,
                        // 'role' => $user->roleItem,
                        'permissions' => $user->permissionsStudent,
                        'access_token' => $user->access_token,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                } elseif ($isMain == 1) {

                    $role = Yii::$app->request->get('role');

                    $roleResult = $user->getRolesNoStudent($role);
                    if (isset($roleResult['active_role'])) {
                        $activeRole = $roleResult['active_role'];
                    } else {
                        $activeRole = null;
                    }
                    User::attachRole($activeRole);

                    if (isset($roleResult['all_roles'])) {
                        $roles = $roleResult['all_roles'];
                    } else {
                        $roles = null;
                    }

                    if (isset($roleResult['permission'])) {
                        $permissions = $roleResult['permission'];
                    } else {
                        $permissions = null;
                    }

                    $data = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'last_name' => $profile->last_name,
                        'first_name' => $profile->first_name,
                        'active_role' => $activeRole,
                        //                        'role' => $user->getRolesNoStudent($role),
                        'role' => $roles,
                        'oferta' => $user->getOfertaIsComformed(),
                        'is_changed' => $user->is_changed,
                        'profile' => $profile,
                        'email' => $user->email,
                        //                        'permissions' => $user->permissionsNoStudent,
                        'change_password_type' => $user->change_password_type,
                        'permissions' => $permissions,
                        'access_token' => $user->access_token,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                } else {
                    $data = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'last_name' => $profile->last_name,
                        'first_name' => $profile->first_name,
                        // 'role' => $user->getRoles(),
                        'role' => $user->getRoles(),
                        'oferta' => $user->getOfertaIsComformed(),
                        // 'role' => $user->roleItem,
                        'change_password_type' => $user->change_password_type,
                        'permissions' => $user->permissionsAll,
                        'access_token' => $user->access_token,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                }
            } else {
                $errors[] = [_e('User is not active.')];
                return $this->response(1, _e('User is not active'), $data, null, ResponseStatus::UNAUTHORIZED);
            }
            if (count($errors) == 0) {
                return $this->response(1, _e('User successfully refreshed'), $data, null, ResponseStatus::OK);
            } else {
                return ['is_ok' => false, 'errors' => simplify_errors($errors)];
            }
        } else {
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }
    }

    public function actionLogout()
    {
        if (Login::logout()) {
            return $this->response(1, _e('User successfully Log Out'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('User not found'), null, null, ResponseStatus::NOT_FOUND);
        }
    }

    public function actionIndex($lang)
    {
        $model = new User();

        $query = $model->find()
            ->with(['profile'])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->andWhere(['users.deleted' => 0])
            ->groupBy('profile.user_id')
            ->andWhere(['not in', 'auth_assignment.item_name', ['admin' , currentRole()]])
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('query')]);


        if (currentRole() != 'admin') {

            $auth = AuthChild::find()
                ->select('child')
                ->where(['in', 'parent', currentRole()]);

            $query->andFilterWhere(['in', 'auth_assignment.item_name', $auth]);

//            $userIds = AuthAssignment::find()
//                ->select('user_id')
//                ->where([
//                    'in', 'auth_assignment.item_name',
//                    AuthChild::find()->select('child')->where([
//                        'in', 'parent', currentRole()
//                    ])
//                ]);

            // faculty
            if (isRole('mudir')) {
                $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID, 2);
                // // kafedra
                if ($k['status'] == 1) {
                    $query->andFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $k['UserAccess'],
                            'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                            'is_deleted' => 0,
                            'status' => 1,
                        ])
                    ]);
                } else {
                    $query->andFilterWhere([
                        'users.id' => -1
                    ]);
                }
            }


            if (isRole('dean')) {
                $dean = get_dean();
            } elseif (isRole('dean_deputy')) {
                $dean = get_dean_deputy();
            }

            if (isRole('dean') || isRole('dean_deputy')) {
                if ($dean) {
                    $query->andFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $dean->id,
                            'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                            'is_deleted' => 0,
                            'status' => 1,
                        ])
                    ])->orFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => Kafedra::find()->select('id')->where([
                                'faculty_id' => $dean->id,
                                'status' => 1,
                                'is_deleted' => 0,
                            ]),
                            'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                            'is_deleted' => 0,
                            'status' => 1,
                        ])
                    ]);
                } else {
                    $query->andFilterWhere([
                        'users.id' => -1
                    ]);
                }
            }

            // department
            if (isRole('dep_lead')) {
                $d = $this->isSelf(Department::USER_ACCESS_TYPE_ID, 2);
                if ($d['status'] == 1) {
                    $query->andFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $d['UserAccess'],
                            'user_access_type_id' => Department::USER_ACCESS_TYPE_ID,
                            'is_deleted' => 0,
                            'status' => 1,
                        ])
                    ]);
                } else {
                    $query->andFilterWhere([
                        'users.id' => -1
                    ]);
                }
            }
        }


        $kafedraId = Yii::$app->request->get('kafedra_id');
        if (isset($kafedraId)) {
            $query->andFilterWhere([
                'in', 'users.id', UserAccess::find()->select('user_id')->where([
                    'table_id' => $kafedraId,
                    'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                    'status' => 1,
                    'is_deleted' => 0,
                ])
            ]);
        }

        $facultyId = Yii::$app->request->get('faculty_id');
        if (isset($facultyId)) {
            $query->andFilterWhere([
                'in', 'users.id', UserAccess::find()->select('user_id')->where([
                    'table_id' => $facultyId,
                    'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                    'status' => 1,
                    'is_deleted' => 0,
                ])
            ]);
        }

        $filter = Yii::$app->request->get('filter');
        //  Filter from Profile
        $profile = new Profile();
        if (isset($filter)) {
            $filter = json_decode(str_replace("'", "", $filter));
            if (isset($filter)) {
                foreach ($filter as $attribute => $value) {
                    $attributeMinus = explode('-', $attribute);
                    if (isset($attributeMinus[1])) {
                        if ($attributeMinus[1] == 'role_name') {
                            if (is_array($value)) {
                                $query = $query->andWhere(['not in', 'auth_assignment.item_name', $value]);
                            }
                        }
                    }
                    if ($attribute == 'role_name') {
                        if (is_array($value)) {
                            $query = $query->andWhere(['in', 'auth_assignment.item_name', $value]);
                        } else {
                            $query = $query->andFilterWhere(['like', 'auth_assignment.item_name', '%' . $value . '%', false]);
                        }
                    }
                    if (in_array($attribute, $profile->attributes())) {
                        $query = $query->andFilterWhere(['profile.' . $attribute => $value]);
                    }
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        if (isset($queryfilter)) {
            $queryfilter = json_decode(str_replace("'", "", $queryfilter));
            if (isset($queryfilter)) {
                foreach ($queryfilter as $attributeq => $word) {
                    if (in_array($attributeq, $profile->attributes())) {
                        $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                    }
                }
            }
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        // $data = $query->all();

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate()
    {
        $model = new User();
        $profile = new Profile();
        $post = Yii::$app->request->post();

        $this->load($model, $post);
        $this->load($profile, $post);
        //        dd($profile);
        $result = User::createItem($model, $profile, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('User successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($id)
    {
        $model = User::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $user = current_user();
        $isChild =
            AuthChild::find()
                ->where(['in', 'child', current_user_roles_array($model->id)])
                ->andWhere(['parent' => $user->attach_role])
                ->all();

        $isChildTwo =
            AuthChild::find()
                ->where(['child' => $user->attach_role])
                ->andWhere(['in' , 'parent' , current_user_roles_array($model->id)])
                ->all();

        if ((count($isChild) == 0 || count($isChildTwo) > 0) && !isRole('admin')) return $this->response(0, _e('You can not get.'), null, null, ResponseStatus::NOT_FOUND);

        if (isRole('dean')) {
            $f = get_dean();
            $isMine = $this->isMine(UserAccess::FACULTY, $f->id, $model->id);
            if (!$isMine) {
                return $this->response(0, _e('You can not get.'), null, null, ResponseStatus::NOT_FOUND);
            }
        } elseif (isRole('mudir')) {
            $m = get_mudir();
            $isMine = $this->isMine(UserAccess::KAFEDRA, $m->id, $model->id);
            if (!$isMine) {
                return $this->response(0, _e('You can not get.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }

        $profile = $model->profile;
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $this->load($profile, $post);
        $data = UploadedFile::getInstancesByName('all_file');
        if ($data) {
            $result_all_file = User::allFileSave($model, $data, $profile);
            if (is_array($result_all_file)) {
                $profile->all_file = json_encode($result_all_file);
                if ($profile->save()) {
                    //                    dd(json_decode($profile->all_file));
                    return $this->response(1, _e('User All File successfully created.'), $model, null, ResponseStatus::OK);
                } else {
                    $last = end($result_all_file);
                    unset($last[0]->url);
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $result_all_file, ResponseStatus::UPROCESSABLE_ENTITY);
                }
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result_all_file, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }
        $result = User::updateItem($model, $profile, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('User successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionSelfget()
    {
        $model = User::findOne(current_user_id());
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);

        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
    }

    public function actionSelf()
    {
        $model = User::findOne(current_user_id());
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $profile = $model->profile;
        $post = Yii::$app->request->post();

        if (isset($post['username'])) {
            unset($post['username']);
        }
        if (isset($post['access_token'])) {
            unset($post['access_token']);
        }
        if (isset($post['access_token_time'])) {
            unset($post['access_token_time']);
        }
        if (isset($post['password_reset_token'])) {
            unset($post['password_reset_token']);
        }
        if (isset($post['status'])) {
            unset($post['status']);
        }
        if (isset($post['deleted'])) {
            unset($post['deleted']);
        }
        if (isset($post['password_hash'])) {
            unset($post['password_hash']);
        }

        $this->load($model, $post);
        $this->load($profile, $post);
        $result = User::selfUpdateItem($model, $profile, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Your data successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionView($id)
    {
        $model = User::find()
            ->with(['profile'])
            ->join('INNER JOIN', 'profile', 'profile.user_id = users.id')
            ->andWhere(['users.id' => $id, 'users.deleted' => 0])
            ->one();

        $user = current_user();
        $isChild =
            AuthChild::find()
                ->where(['in', 'child', current_user_roles_array($model->id)])
                ->andWhere(['parent' => $user->attach_role])
                ->all();

        $isChildTwo =
            AuthChild::find()
                ->where(['child' => $user->attach_role])
                ->andWhere(['in' , 'parent' , current_user_roles_array($model->id)])
                ->all();

        if ((count($isChild) == 0 || count($isChildTwo) > 0) && !isRole('admin')) return $this->response(0, _e('You can not get.'), null, null, ResponseStatus::NOT_FOUND);


        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($id)
    {
        //        return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        $post = Yii::$app->request->post();
        if (isset($post['url'])) {
            //            $profileAllFileDeleted = Profile::findOne(['user_id' => $id]);
            //            dd(json_decode($profileAllFileDeleted->all_file));
            $url = $post['url'];
            $delete = User::deleteAllFile($id, $url);
            //            dd($delete);
            if (!is_array($delete)) {
                return $this->response(1, _e('File successfully deleted.'), null, null, ResponseStatus::OK);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $delete, ResponseStatus::BAD_REQUEST);
            }
        }
        $result = User::deleteItem($id);
        if (!is_array($result)) {
            return $this->response(1, _e('User successfully deleted.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::BAD_REQUEST);
        }
    }

    //    public function actionAllFileRemoves($id)
    //    {
    //        $post = Yii::$app->request->post();
    //        dd(3221);
    //        $result = User::deleteItem($id);
    //        if (!is_array($result)) {
    //            return $this->response(1, _e('User successfully deleted.'), null, null, ResponseStatus::OK);
    //        } else {
    //            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::BAD_REQUEST);
    //        }
    //    }

    public function actionLoginHistory($lang , $id)
    {
        $model = new LoginHistory();

        $query = $model->find();
        if (isRole('admin') || isRole('edu_admin')) {
            $query = $query->andWhere(['user_id' => $id]);
        } else {
            $query = $query->andWhere(['user_id' => current_user_id()]);
        }

        $get = Yii::$app->request->get('date');
        if ($get != null) {

            $start = date("Y-m-d 00:00:00" , strtotime($get));
            $end = date("Y-m-d 23:59:59" , strtotime($get));

            $query = $query->andWhere(['>=' , 'created_on' , $start])
                ->andWhere(['<=' , 'created_on' , $end]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }



    public function actionStatusList()
    {
        return $this->response(1, _e('Success.'), User::statusList(), null, ResponseStatus::OK);
    }
}
