<?php

namespace api\resources;

use Yii;
use api\resources\Profile;
use common\models\Student;
use yii\web\UploadedFile;

class StudentUser extends ParentUser
{
    public static $roleList = ['student', 'master'];



    public static function createItemImport($model, $profile, $student, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // Validatin input data

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // role to'gri jo'natilganligini tekshirish
        if (!(isset($post['role']) && !empty($post['role']) && is_string($post['role']))) {
            $errors[] = ['role' => [_e('Role is not valid.')]];
        }

        if (isset($post['role'])) {
            // Role mavjudligini tekshirish
            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole($post['role']);
            if (!$authorRole) {
                $errors[] = ['role' => [_e('Role not found.')]];
            }

            // rolening student toifasidagi rollar tarkibidaligini tekshirish
            if (!in_array($post['role'], self::$roleList)) {
                $errors[] = ['role' => [_e('Role does not fit the type of staff.')]];
            }
        }
        // **********

        if (count($errors) == 0) {
            if (isset($post['password']) && !empty($post['password'])) {
                $password = $post['password'];
            } else {
                $password = _passwordMK();
            }

            $model->password_hash = \Yii::$app->security->generatePasswordHash($password);

            $model->auth_key = \Yii::$app->security->generateRandomString(20);
            $model->password_reset_token = null;
            $model->access_token = \Yii::$app->security->generateRandomString();
            $model->access_token_time = time();
            // $model->save();

            if ($model->save()) {
                //**parolni shifrlab saqlaymiz */
                $model->savePassword($password, $model->id);
                //**** */
                $profile->user_id = $model->id;
                if (!$profile->save()) {
                    $errors[] = $profile->errors;
                } else {
                    $student->user_id = $model->id;
                    if (!$student->save()) {
                        $errors[] = $student->errors;
                    } else {
                        // role ni userga assign qilish
                        $auth->assign($authorRole, $model->id);
                    }
                }
            } else {
                $errors[] = $model->errors;
            }
        }
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function createItem($model, $profile, $student, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // Validatin input data

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // role to'gri jo'natilganligini tekshirish
        if (!(isset($post['role']) && !empty($post['role']) && is_string($post['role']))) {
            $errors[] = ['role' => [_e('Role is not valid.')]];
        }

        if (isset($post['role'])) {
            // Role mavjudligini tekshirish
            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole($post['role']);
            if (!$authorRole) {
                $errors[] = ['role' => [_e('Role not found.')]];
            }

            // rolening student toifasidagi rollar tarkibidaligini tekshirish
            if (!in_array($post['role'], self::$roleList)) {
                $errors[] = ['role' => [_e('Role does not fit the type of staff.')]];
            }
        }

        // **********

        if (count($errors) == 0) {
            if (isset($post['password']) && !empty($post['password'])) {
                $password = $post['password'];
            } else {
                $password = _passwordMK();
            }

            $model->password_hash = \Yii::$app->security->generatePasswordHash($password);

            $model->auth_key = \Yii::$app->security->generateRandomString(20);
            $model->password_reset_token = null;
            $model->access_token = \Yii::$app->security->generateRandomString();
            $model->access_token_time = time();

            if ($model->save(false)) {

                //**parolni shifrlab saqlaymiz */
                $model->savePassword($password, $model->id);
                //**** */

                $profile->user_id = $model->id;

                // avatarni saqlaymiz
                $model->avatar = UploadedFile::getInstancesByName('avatar');
                if ($model->avatar) {
                    $model->avatar = $model->avatar[0];
                    $avatarUrl = $model->upload();
                    if ($avatarUrl) {
                        $profile->image = $avatarUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
                // ***


                // Passport file ni saqlaymiz
                $model->passport_file = UploadedFile::getInstancesByName('passport_file');
                if ($model->passport_file) {
                    $model->passport_file = $model->passport_file[0];
                    $passportUrl = $model->uploadPassport();
                    if ($passportUrl) {
                        $profile->passport_file = $passportUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
                // ***

                if (!$profile->save()) {
                    $errors[] = $profile->errors;
                } else {
                    $student->user_id = $model->id;
                    if (!$student->save()) {
                        $errors[] = $student->errors;
                    } else {
                        // role ni userga assign qilish
                        $auth->assign($authorRole, $model->id);
                    }
                }
            } else {
                $errors[] = $model->errors;
            }
        }
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function updateItemImport($model, $profile, $student, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // if (!($model->validate())) {
        //     $errors[] = $model->errors;
        // }
        // if (!($profile->validate())) {
        //     $errors[] = $profile->errors;
        // }
        // if (!($student->validate())) {
        //     $errors[] = $student->errors;
        // }


        if (isset($post['role'])) {

            // role to'gri jo'natilganligini tekshirish
            if (empty($post['role']) || !is_string($post['role'])) {
                $errors[] = ['role' => [_e('Role is not valid.')]];
            }

            // Role mavjudligini tekshirish
            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole($post['role']);
            if (!$authorRole) {
                $errors[] = ['role' => [_e('Role not found.')]];
            }

            // rolening student toifasidagi rollar tarkibidaligini tekshirish
            if (!in_array($post['role'], self::$roleList)) {
                $errors[] = ['role' => [_e('Role does not fit the type of staff.')]];
            }
        }

        if (count($errors) == 0) {

            if (isset($post['password']) && !empty($post['password'])) {
                $password = $post['password'];
                $model->password_hash = \Yii::$app->security->generatePasswordHash($password);
                //**parolni shifrlab saqlaymiz */
                $model->savePassword($password, $model->id);
                //**** */
            }

            if ($model->save()) {

                // avatarni saqlaymiz
                $model->avatar = UploadedFile::getInstancesByName('avatar');
                if ($model->avatar) {
                    $model->avatar = $model->avatar[0];
                    $avatarUrl = $model->upload();
                    if ($avatarUrl) {
                        $profile->image = $avatarUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
                // ***

                // Passport file ni saqlaymiz
                $model->passport_file = UploadedFile::getInstancesByName('passport_file');
                if ($model->passport_file) {
                    $model->passport_file = $model->passport_file[0];
                    $passportUrl = $model->uploadPassport();
                    if ($passportUrl) {
                        $profile->passport_file = $passportUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
                // ***

                if (!$profile->save(false)) {
                    $errors[] = $profile->errors;
                } else {
                    if ($student->save()) {
                        if (isset($post['role'])) {
                            // user ning eski rolini o'chirish
                            $auth->revokeAll($model->id);
                            // role ni userga assign qilish
                            $auth->assign($authorRole, $model->id);
                        }
                    } else {
                        $errors[] = $student->errors;
                    }
                }
            } else {
                $errors[] = $model->errors;
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }
    

    public static function updateImage($model, $profile, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // if (!($model->validate())) {
        //     $errors[] = $model->errors;
        // }
        // if (!($profile->validate())) {
        //     $errors[] = $profile->errors;
        // }
        // if (!($student->validate())) {
        //     $errors[] = $student->errors;
        // }

        if (count($errors) == 0) {
            // avatarni saqlaymiz
            // if (isRole('edu_admin') || isRole('admin')) {
            $model->avatar = UploadedFile::getInstancesByName('avatar');
            if ($model->avatar) {
                $model->avatar = $model->avatar[0];
                $avatarUrl = $model->upload();
                if ($avatarUrl) {
                    $profile->image = $avatarUrl;
                } else {
                    $errors[] = $model->errors;
                }
            } else {

                $errors[] = _e('Image not send');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        // ***

        // Passport file ni saqlaymiz
        $model->passport_file = UploadedFile::getInstancesByName('passport_file');
        if ($model->passport_file) {
            $model->passport_file = $model->passport_file[0];
            $passportUrl = $model->uploadPassport();
            if ($passportUrl) {
                $profile->passport_file = $passportUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***

        if (!$profile->save(false)) {
            $errors[] = $profile->errors;
        }


        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function deleteItem($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model = Student::findOne($id);

        if (!isset($model)) {
            $errors[] = [_e('Student not found.')];
        } else {
            $userId = $model->user_id;
        }

        if (count($errors) == 0) {

            // remove student
            $studentDeleted = Student::findOne(['id' => $id]);
            if (!$studentDeleted) {
                $errors[] = [_e('Error in student deleting process.')];
            } elseif ($studentDeleted->is_deleted == 1) {
                $errors[] = [_e('Student not found')];
            } else {
                $studentDeleted->is_deleted = 1;
                $studentDeleted->save(false);
            }

            // remove profile
            $profileDeleted = Profile::findOne(['user_id' => $userId]);
            if (!$profileDeleted) {
                $errors[] = [_e('Error in profile deleting process.')];
            } else {
                $profileDeleted->is_deleted = 1;
                $profileDeleted->save(false);
            }

            // remove model
            $userDeleted = User::findOne($userId);
            if (!$userDeleted) {
                $errors[] = [_e('Error in user deleting process.')];
            } else {
                $userDeleted->status = User::STATUS_BANNED;
                $userDeleted->save(false);
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function findStudent($id)
    {
        return self::find()
            ->with(['profile', 'user'])
            ->leftJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->where(['and', ['id' => $id], ['in', 'auth_assignment.item_name', self::$roleList]])
            ->one();
    }
}
