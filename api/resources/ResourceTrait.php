<?php

namespace api\resources;

use common\models\model\Student;
use common\models\model\TeacherAccess;
use common\models\model\Translate;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

trait ResourceTrait
{

    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::class,
            ],
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function loadApi($data)
    {
        return $this->load($data, '');
    }

    /**
     * Get created by
     *
     * @return void
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Get created by
     *
     * @return void
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Get created At
     *
     * @return void
     */
    public function getCreatedAt()
    {
        return date('Y-m-d H:i:s', $this->created_at);
    }

    /**
     * Get created At
     *
     * @return void
     */
    public function getUpdatedAt()
    {
        return date('Y-m-d H:i:s', $this->updated_at);
    }


    public static function createFromTable($nameArr, $table_name, $model_id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        foreach ($nameArr as $key => $value) {

            $new_translate[$key] = new Translate();
            $new_translate[$key]->name = $value;
            $new_translate[$key]->table_name = $table_name;
            $new_translate[$key]->model_id = $model_id;
            if ($new_translate[$key]->save()) {
                $transaction->commit();
                return true;
            } else {
                $errors[] = $new_translate[$key]->getErrorSummary(true);
                return simplify_errors($errors);
            }
        }
    }

    public static function teacher_access_user_id($teacher_access_id)
    {
        $teacherAccess = TeacherAccess::findOne($teacher_access_id);
        return $teacherAccess ? $teacherAccess->user_id : null;
    }

    public static function student_now($type = 1, $user_id = null)
    {
        // Agar $user_id berilmagan bo'lsa, uning qiymatini joriy foydalanuvchidan oling
        $user_id = $user_id ?? current_user_id();

        // Talabani toping, aks holda null qaytaring
        $student = Student::findOne(['user_id' => $user_id]);
        if ($student === null) {
            return null;
        }

        // Turiga qarab natijani qaytaring
        return ($type === 1) ? $student->id : $student;
    }
    // public static function student_now00($type = null, $user_id = null)
    // {
    //     if ($user_id == null) {
    //         $user_id = current_user_id();
    //     }
    //     if ($type == null) {
    //         $type = 1;
    //     }
    //     $student = Student::findOne(['user_id' => $user_id]);
    //     if ($type == 1) {
    //         return  $student->id ?? null;
    //     } elseif ($type == 2) {
    //         return  $student ?? null;
    //     }
    // }

    public static function student($type = 1, $user_id = null)
    {
        // Agar $user_id berilmagan bo'lsa, uning qiymatini joriy foydalanuvchidan oling
        $user_id = $user_id ?? current_user_id();

        // Talabani toping, aks holda null qaytaring
        $student = Student::findOne(['user_id' => $user_id]);
        if ($student === null) {
            return null;
        }

        // Turiga qarab natijani qaytaring
        if ($type === 1) {
            return $student->id;
        }

        if ($type === 2) {
            return $student;
        }
    }
    // public static function student00($type = null, $user_id = null)
    // {
    //     if ($user_id == null) {
    //         $user_id = current_user_id();
    //     }
    //     if ($type == null) {
    //         $type = 1;
    //     }
    //     $student = Student::findOne(['user_id' => $user_id]);
    //     if ($type == 1) {
    //         return  $student->id ?? null;
    //     } elseif ($type == 2) {
    //         return  $student ?? null;
    //     }
    // }

    public static function findByStudentId($id, $type = 1)
    {
        $student = Student::findOne(['id' => $id]);

        // Early return if no student found
        if ($student === null) {
            return null;
        }

        if ($type === 1) {
            return $student->user_id;
        }

        if ($type === 2) {
            return $student;
        }

        // Optionally, handle invalid type values here
    }
    // public static function findByStudentId00($id, $type = null)
    // {
    //     if ($type == null) {
    //         $type = 1;
    //     }
    //     $student = Student::findOne(['id' => $id]);
    //     if ($type == 1) {
    //         return  $student->user_id ?? null;
    //     } elseif ($type == 2) {
    //         return  $student ?? null;
    //     }
    // }

    public static function teacher_access($type = 1, $select = ['id'], $user_id = null)
    {
        // Set the default user_id to current_user_id if null
        $user_id = $user_id ?? current_user_id();

        // Common query part
        $query = TeacherAccess::find()
            ->where(['user_id' => $user_id, 'is_deleted' => 0])
            ->andWhere([
                'in',
                'subject_id',
                Subject::find()
                    ->where(['is_deleted' => 0])
                    ->select('id')
            ])
            ->select($select);

        if ($type === 1) {
            return $query;
        }

        if ($type === 2) {
            return $query->asArray()->all();
        }

        // Optionally, handle invalid type values here
    }
    // public static function teacher_access00($type = null, $select = [], $user_id = null)
    // {
    //     if (is_null($user_id)) {
    //         $user_id = current_user_id();
    //     }

    //     if (is_null($type)) {
    //         $type = 1;
    //     }

    //     if (empty($select)) {
    //         $select = ['id'];
    //     }
    //     if ($type == 1) {
    //         return TeacherAccess::find()
    //             ->where(['user_id' => $user_id, 'is_deleted' => 0])
    //             ->andWhere(['in', 'subject_id', Subject::find()
    //                 ->where(['is_deleted' => 0])
    //                 ->select('id')])
    //             ->select($select);
    //     } elseif ($type == 2) {
    //         return TeacherAccess::find()
    //             ->asArray()
    //             ->where(['user_id' => $user_id, 'is_deleted' => 0])
    //             ->andWhere(['in', 'subject_id', Subject::find()
    //                 ->where(['is_deleted' => 0])
    //                 ->select('id')])
    //             ->select($select)

    //             ->all();
    //     }
    // }

    public static function encodemk5MK($key)
    {
        return str_replace('=', '', base64_encode(base64_encode($key . "DEV"))); // Encode string using Base64 // Output: SGVsbG8gV29ybGQh
    }

    public static function decodemk5MK($key)
    {
        return base64_decode($key); // Encode string using Base64 // Output: SGVsbG8gV29ybGQh
    }

    public static function encodeMK($key)
    {
        $str = '';
        foreach (str_split((string) $key) as $one) {

            $symKey = (int)$one + 97;
            $str .= chr($symKey);
        }
        return $str;
    }


    public static function decodeFromLetterMK($string)
    {
        // return $string;
        // $string = "ejdg-biebc";
        $num = '';
        foreach (str_split((string) $string) as $one) {
            if ($one == "-") {
                $num .= $one;
            } else {
                $num .= ((int)ord($one) - 97);
            }
        }
        return $num;
    }
    public static function decodeMK($string)
    {
        // return $string;
        // $string = "ejdg-biebc";
        $num = '';
        foreach (str_split((string) $string) as $one) {
            if ($one == "-") {
                $num .= $one;
            } else {
                $num .= ((int)ord($one) - 97);
            }
        }
        return $num;
    }
}
