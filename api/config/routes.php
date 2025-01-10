<?php

$controllers = [
    'passport-get-data',
    'room-ip',
    'student-group',
    'timetable-reason',
    'timetable-attend',
    'timetable-date',
    'timetable',
    'student-vedomst',
    'student-semestr-subject',
    'final-exam',
    'final-exam-test',
    'final-exam-test-start',
    'final-exam-test-question',
    'document-decree',
    'document-notification',
    'action-log',
    'letter-outgoing',
    'letter-reply',
    'letter-forward-item',
    'letter-forward',
    'letter',
    'important-level',
    'document-execution',
    'document-weight',
    'document-type',
    'document',
    'exam-questions',
    'exam',
    'exam-student',
    'exam-student-question',
    'exam-test-student-answer',
    'exam-control-student',
    'exam-control',
    'exam-test',
    'exam-test-option',
    'commands',
    'commands-type',
    'work-load',
    'work-rate',
    'job-load-rate',
    'student-attend',
    'academic-degree',
    'degree',
    'degree-info',
    'diploma-type',
    'partiya',
    'types-arrays',
    'room-type',
    'profile',
    'group',
    'get-teacher',
    'student',
    'student-mark',
    'user',
    'department',
    'subject',
    'subject-semestr',
    'subject-topic',
    'nationality',
    'languages',
    'lang',
    //    'residence-type',
    //    'science-degree',
    //    'scientific-title',
    //    'special-title',
    'basis-of-learning',
    'building',
    'course',
    'room',
    'course',
    'direction',
    'faculty',
    'kafedra',
    'para',
    'semestr',
    'edu-year',
    'subject',
    'subject-type',
    'subject-category',
    'exams-type',
    'edu-type',
    'edu-form',
    'edu-plan',
    'edu-semestr',
    'edu-semestr-exams-type',
    'edu-semestr-subject',
    'edu-semestr-subject-category-time',
    'teacher-access',
    'password',
    'translate',
    'week',
    'region',
    'area',
    //    'student-exam',
    'country',
    'attend',
    'attend-reason',
    'test',
    'option',

//    'time-table',
    'student-topic-permission',
    //    'time-option',
    //    'student-time-table',
    //    'student-time-option',

    //    'exam',
    //    'exam-student',
    //    'exam-question',
    //    'exam-question-type',
    //    'exam-question-option',
    //    'exam-student-answer',
    //    'exam-teacher-check',

    //    'subject-sillabus',

    //    'question',
    //    'question-type',
    //    'question-option',
    //    'exam-semeta',

    'user-access-type',
    'user-access',

    'subject-access',

    'subject-content',
    'citizenship',
    //    'notification',
    //    'notification-role',
    'category-of-cohabitant',
    'residence-status',
    'social-category',
    'student-category',


];

$controllerRoutes = [];

foreach ($controllers as $controller) {
    $rule = [
        'class' => 'yii\rest\UrlRule',
        'controller' => $controller,
        'prefix' => '<lang:\w{2}>'
    ];
    if ($controller == 'basis-of-learning') {
        $rule['pluralize'] = false;
    }
    $controllerRoutes[] = $rule;
}

$routes = [

    // Login and get access_token from server
    'POST <lang:\w{2}>/auth/login' => 'auth/login',

    'GET <lang:\w{2}>/auth/bot' => 'auth/bot',

    'GET <lang:\w{2}>/open-data/academik-reference/<key>' => 'get-info/academik-reference',

    // User Self update data
    'PUT <lang:\w{2}>/users/self' => 'user/self',
    // User Get Self data
    'GET <lang:\w{2}>/users/self' => 'user/selfget',

    // Get me
    'GET <lang:\w{2}>/users/me' => 'user/me',
    // Log out
    'POST <lang:\w{2}>/auth/logout' => 'user/logout',

    /** telegram */
    'GET <lang:\w{2}>/telegrams/bot' => 'telegram/bot',
    /** Oferta */
    'POST <lang:\w{2}>/users/oferta/' => 'user/oferta',
    /** Get user statuses */
    'GET <lang:\w{2}>/user-statuses' => 'user/status-list',
    // Student Get me
    'GET <lang:\w{2}>/students/me' => 'student/me',
    'GET <lang:\w{2}>/students/missed-hours' => 'student/missed-hours',

    // Talabaga tutor biriktirish
    'POST <lang:\w{2}>/students/tutor' => 'student/tutor',

    /** MIP pinfl */
    'GET <lang:\w{2}>/users/get/' => 'user/get',
    'GET <lang:\w{2}>/users/login-history/<id>' => 'user/login-history',

    // Roles and permissions endpoint
    'GET <lang:\w{2}>/roles' => 'access-control/roles', // Get roles list
    'GET <lang:\w{2}>/roles/<role>/permissions' => 'access-control/role-permissions', // Get role permissions
    'POST <lang:\w{2}>/roles' => 'access-control/create-role', // Create new role
    'POST <lang:\w{2}>/create-permission' => 'access-control/create-permission', // Create new permission
    'PUT <lang:\w{2}>/roles' => 'access-control/update-role', // Update role
    'DELETE <lang:\w{2}>/roles/<role>' => 'access-control/delete-role', // Delete role
    'GET <lang:\w{2}>/permissions' => 'access-control/permissions', // Get permissions list

    // Department type list
    'GET <lang:\w{2}>/departments/types' => 'department/types',
    'GET <lang:\w{2}>/departments/parents' => 'department/parent',

    // passport-get-data
    'GET <lang:\w{2}>/passport-get-data/ik' => 'passport-get-data/ik',
    'GET <lang:\w{2}>/passport-get-data/pinfl' => 'passport-get-data/pinfl',

    // Faculty UserAccess fakultitetga user biriktirish
    'POST <lang:\w{2}>/faculties/user-access' => 'faculty/user-access',
    // Kafedra UserAccess fakultitetga user biriktirish
    'POST <lang:\w{2}>/kafedras/user-access' => 'kafedra/user-access',
    // Department UserAccess fakultitetga user biriktirish
    'POST <lang:\w{2}>/departments/user-access' => 'department/user-access',

    // Excel file yuklash
    'POST <lang:\w{2}>/excels/ik-excels' => 'excel/ik-excel',

    'POST <lang:\w{2}>/subject-topics/export' => 'subject-topic/export',

    // Letter
    'PUT <lang:\w{2}>/letters/is-ok/<id>' => 'letter/is-ok',

    // Document
    'DELETE <lang:\w{2}>/documents/delete-file/<id>' => 'document/delete-file',

    // Letter Reply
    'PUT <lang:\w{2}>/letter-replies/is-ok/<id>' => 'letter-reply/is-ok',

    // Letter OutGoing
    'PUT <lang:\w{2}>/letter-outgoings/is-ok/<id>' => 'letter-outgoing/is-ok',

    // Letter Files
    'DELETE <lang:\w{2}>/letters/delete-file/<id>' => 'letter/delete-file',

    // Student Mark
    'POST <lang:\w{2}>/student-marks/student-mark-update' => 'student-mark/student-mark-update',
    'POST <lang:\w{2}>/student-marks/exam' => 'student-mark/exam',

    'PUT <lang:\w{2}>/student-marks/final-exam/<id>' => 'student-mark/final-exam',
    'GET <lang:\w{2}>/student-marks/get' => 'student-mark/get',

    // Final Exam
    'PUT <lang:\w{2}>/final-exams/confirm/<id>' => 'final-exam/confirm',
    'PUT <lang:\w{2}>/final-exams/confirm-two/<id>' => 'final-exam/confirm-two',
    'PUT <lang:\w{2}>/final-exams/in-charge/<id>' => 'final-exam/in-charge',
    'PUT <lang:\w{2}>/final-exams/confirm-mudir/<id>' => 'final-exam/confirm-mudir',
    'PUT <lang:\w{2}>/final-exams/confirm-dean/<id>' => 'final-exam/confirm-dean',
    'PUT <lang:\w{2}>/final-exams/last-confirm/<id>' => 'final-exam/last-confirm',
    'POST <lang:\w{2}>/final-exams/all-confirm' => 'final-exam/all-confirm',
    'PUT <lang:\w{2}>/final-exams/edu-type-update/<id>' => 'final-exam/edu-type-update',

    // Final Exam Test
    'PUT <lang:\w{2}>/final-exam-tests/add/<id>' => 'final-exam-test/add',

    'PUT <lang:\w{2}>/tests/all-delete/<id>' => 'test/all-delete',

    // Final Exam Test Start
    'PUT <lang:\w{2}>/final-exam-test-starts/add-ball/<id>' => 'final-exam-test-start/add-ball',
    'PUT <lang:\w{2}>/final-exam-test-starts/student-update/<id>' => 'final-exam-test-start/student-update',
    'PUT <lang:\w{2}>/final-exam-test-starts/finish/<id>' => 'final-exam-test-start/finish',
    'GET <lang:\w{2}>/final-exam-test-starts/time' => 'final-exam-test-start/time',
    'GET <lang:\w{2}>/final-exam-test-starts/get/<id>' => 'final-exam-test-start/get',

    // Document Notification
    'PUT <lang:\w{2}>/document-notifications/hr-update/<id>' => 'document-notification/hr-update',
    'PUT <lang:\w{2}>/document-notifications/signature-update/<id>' => 'document-notification/signature-update',
    'GET <lang:\w{2}>/document-notifications/sign' => 'document-notification/sign',
    'GET <lang:\w{2}>/document-notifications/confirm' => 'document-notification/confirm',
    'PUT <lang:\w{2}>/document-notifications/command-type/<id>' => 'document-notification/command-type',


    // Document Decree
    'PUT <lang:\w{2}>/document-decrees/hr-update/<id>' => 'document-decree/hr-update',
    'PUT <lang:\w{2}>/document-decrees/signature-update/<id>' => 'document-decree/signature-update',
    'GET <lang:\w{2}>/document-decrees/sign' => 'document-decree/sign',
    'GET <lang:\w{2}>/document-decrees/confirm' => 'document-decree/confirm',
    'PUT <lang:\w{2}>/document-decrees/command-type/<id>' => 'document-decree/command-type',


    // Exam
    'PUT <lang:\w{2}>/exams/exam-finish/<id>' => 'exam/exam-finish',
    'PUT <lang:\w{2}>/exams/exam-check/<id>' => 'exam/exam-check',
    'PUT <lang:\w{2}>/exams/allotment/<id>' => 'exam/allotment',
    'PUT <lang:\w{2}>/exams/exam-notify/<id>' => 'exam/exam-notify',
    'PUT <lang:\w{2}>/exams/exam-teacher-attach/<id>' => 'exam/exam-teacher-attach',

    // Exam Student Question
    'PUT <lang:\w{2}>/exam-student-questions/update-ball/<id>' => 'exam-student-question/update-ball',

    // Exam Student
    'POST <lang:\w{2}>/exam-students/check' => 'exam-student/check',
    'PUT <lang:\w{2}>/exam-students/finish/<id>' => 'exam-student/finish',
    'PUT <lang:\w{2}>/exam-students/rating/<id>' => 'exam-student/rating',

    // Exam Control test belgilash
    'POST <lang:\w{2}>/exam-test-student-answers/designation' => 'exam-test-student-answer/designation',
    'PUT <lang:\w{2}>/exam-control-students/finish/<id>' => 'exam-control-student/finish',
    'PUT <lang:\w{2}>/exam-control-students/rating/<id>' => 'exam-control-student/rating',
    'PUT <lang:\w{2}>/exam-control-students/check/<id>' => 'exam-control-student/check',

    // Exam Test Excel file yuklash
//    'POST <lang:\w{2}>/exam-tests/excel-import' => 'exam-test/excel-import',
    'POST <lang:\w{2}>/tests/excel-import' => 'test/excel-import',
    'POST <lang:\w{2}>/room-ips/excel-import' => 'room-ip/excel-import',

    // Exam Questions
    'PUT <lang:\w{2}>/exam-questions/is-confirm/<id>' => 'exam-questions/is-confirm',


    /** Student from Hemis via pinfl */
    'GET <lang:\w{2}>/students/get/' => 'student/get',

    /**  Student Topic Permission   */

    'POST <lang:\w{2}>/student-topic-permissions/permissions' => 'student-topic-permission/permission',
    'POST <lang:\w{2}>/subject-topic-tests/topic-tests' => 'subject-topic-test/topic-test',
    'POST <lang:\w{2}>/subject-topic-tests/answers' => 'subject-topic-test/answer',
    'POST <lang:\w{2}>/subject-topic-tests/finishs' => 'subject-topic-test/finish',

    // Time Table add group
    'POST <lang:\w{2}>/time-tables/create-add-group' => 'time-table/create-add-group',

    'PUT <lang:\w{2}>/tests/is-check/<id>/' => 'test/is-check',

    'PUT <lang:\w{2}>/subject-topic/orders/<id>/' => 'subject-topic/order',

    /** Student For turniket */
    //    'GET <lang:\w{2}>/students/by-pinfl/<pinfl>' => 'student/by-pinfl',
    //    'GET <lang:\w{2}>/students/time-option-not/' => 'student/time-option-not',

    /** Hostel Yotoqxona */
    //    'GET <lang:\w{2}>/hostel-docs/check/<id>/' => 'hostel-doc/check',
    //    'GET <lang:\w{2}>/hostel-docs/not/<id>/' => 'hostel-doc/not',

    /** attend-reason  */
    'GET <lang:\w{2}>/attend-reasons/confirm/<id>/' => 'attend-reason/confirm',
    'GET <lang:\w{2}>/attend-reasons/cancellation/<id>/' => 'attend-reason/cancellation',

    // Student Attend
    'GET <lang:\w{2}>/students/statistic-attend/' => 'student/statistic-attend/',

    /** Code Correctors */
    //    'GET <lang:\w{2}>/exam-students/correct/<key>/' => 'exam-student/correct',
    //    'POST <lang:\w{2}>/subject-contents/order' => 'subject-content/order',
    /** Code Correctors */

    /** exam control student appeal 1 va 2  */
    //    'POST <lang:\w{2}>/exam-control-students/appeal/<id>/' => 'exam-control-student/appeal',

    /* statistics all */
    // statistic student-count-by-faculty
        'GET <lang:\w{2}>/statistics/faculty-statistic' => 'statistic/faculty-statistic',

        'GET <lang:\w{2}>/statistics/home-page' => 'statistic/home-page',
    //    'GET <lang:\w{2}>/statistics/kpi-content-store' => 'statistic/kpi-content-store',
    //    'GET <lang:\w{2}>/statistics/kpi-survey-store' => 'statistic/kpi-survey-store',


    // statistic Kafedra Questions Teachers
    //    'GET <lang:\w{2}>/statistics/kafedra' => 'statistic/kafedra',
    //    'GET <lang:\w{2}>/statistics/checking' => 'statistic/checking',
    //    'GET <lang:\w{2}>/statistics/checking-chala' => 'statistic/checking-chala',
    //    'GET <lang:\w{2}>/statistics/exam-checking' => 'statistic/exam-checking',

    // ball statistics two, three, four, five
    //    'GET <lang:\w{2}>/exam-students/ball' => 'exam-student/ball',
    /* statistics all */

    // election password generator
    //    'GET <lang:\w{2}>/elections/<id>/password' => 'election/password',

    // Question status update
    //    'PUT <lang:\w{2}>/questions/status-update/<id>' => 'question/status-update',
    // Question status list
    //    'GET <lang:\w{2}>/questions/status-list' => 'question/status-list',

    // KpiCategory Extra fields, term, tab, status
    //    'GET <lang:\w{2}>/kpi-categories/extra' => 'kpi-category/extra',



    // TimeTable
    'GET <lang:\w{2}>/timetables/edu-plan' => 'timetable/edu-plan',
    'GET <lang:\w{2}>/timetables/edu-semestr/<id>' => 'timetable/edu-semestr',
    'PUT <lang:\w{2}>/timetables/add-day/<id>' => 'timetable/add-day',
    'GET <lang:\w{2}>/timetables/user' => 'timetable/user',
    'GET <lang:\w{2}>/timetables/teacher-topic' => 'timetable/teacher-topic',
    'DELETE <lang:\w{2}>/timetables/delete-one/<id>' => 'timetable/delete-one',
    'POST <lang:\w{2}>/timetables/add-group' => 'timetable/add-group',
    'POST <lang:\w{2}>/timetables/student-type' => 'timetable/student-type',

    'GET <lang:\w{2}>/timetable-dates/get' => 'timetable-date/get',
    'GET <lang:\w{2}>/timetable-dates/attend/<id>' => 'timetable-date/attend',
    'GET <lang:\w{2}>/timetable-dates/filter' => 'timetable-date/filter',
    'GET <lang:\w{2}>/timetable-dates/get-date' => 'timetable-date/get-date',


    'GET <lang:\w{2}>/timetable-attends/get' => 'timetable-attend/get',
    'GET <lang:\w{2}>/timetable-attends/attend-student' => 'timetable-attend/attend-student',

    // exam Distribution
    //    'GET <lang:\w{2}>/exams/<id>/distribution' => 'exam/distribution',
    // exam Appeal Distribution
    //    'GET <lang:\w{2}>/exams/<id>/appeal-distribution' => 'exam/appeal-distribution',
    // exam announced // natijani e'lon qilish
    // 'GET <lang:\w{2}>/exams/<id>/ad' => 'exam/ad',

    // studentga savollarni random tushirish
    //    'POST <lang:\w{2}>/exam-student-answers/get-question' => 'exam-student-answer/get-question',
    // ExamStudentAnswer Appeal checking
    //    'PUT <lang:\w{2}>/exam-checkings/<id>/appeal' => 'exam-checking/appeal',

    // teacherga studentlarni random tushirish
    //    'POST <lang:\w{2}>/exam-teacher-check/random-students' => 'exam-teacher-check/random-students',

    // Subject Content Trash ( get Deleted Content)
    //    'GET <lang:\w{2}>/subject-contents/trash' => 'subject-content/trash',
    // Subject Content Delete from Trash ( get Deleted Content)  bazadan o'chirish
    //    'DELETE <lang:\w{2}>/subject-contents/trash/<id>' => 'subject-content/trash-delete',
    // Subject Content type list
    'GET <lang:\w{2}>/subject-contents/types' => 'subject-content/types',
//    'GET <lang:\w{2}>/subject-contents/types/<id>' => 'subject-content/types',



    /** Free teachers for time tables */
     'GET <lang:\w{2}>/teacher-accesses/free' => 'teacher-access/free',
     'GET <lang:\w{2}>/teacher-accesses/free-exam' => 'teacher-access/free-exam',
     'GET <lang:\w{2}>/teacher-accesses/get' => 'teacher-access/get',
     'GET <lang:\w{2}>/rooms/free' => 'room/free',
     'GET <lang:\w{2}>/rooms/free-exam' => 'room/free-exam',
    // /**  */

    // Student Attendees By
    // 'GET <lang:\w{2}>/student-attends/by-date' => 'student-attend/by-date',


     // Edu Semestr
     'POST <lang:\w{2}>/edu-semestr/student-subjects' => 'edu-semestr/student-subject-merge',


    // Student Import
    // 'POST <lang:\w{2}>/students/import' => 'student/import',
    // // Student Export
     'GET <lang:\w{2}>/students/export' => 'student/export',
//     'POST <lang:\w{2}>/students/read' => 'student/read',
     'POST <lang:\w{2}>/students/type' => 'student/type',

    // My Notifications
    //    'GET <lang:\w{2}>/notifications/my' => 'notification/my',
    // Notifications Status list
    //    'GET <lang:\w{2}>/notifications/status-list' => 'notification/status-list',
    // Notifications Approved (tasdiqlavoring)
    //    'PUT <lang:\w{2}>/notifications/approved/<id>' => 'notification/approved',


    // ***

    /* Enums */
    //    'GET <lang:\w{2}>/genders' => 'enum/genders',
    //    'GET <lang:\w{2}>/educations' => 'enum/educations',
    //    'GET <lang:\w{2}>/education-degrees' => 'enum/education-degrees',
    //    'GET <lang:\w{2}>/disability-groups' => 'enum/disability-groups',
    //    'GET <lang:\w{2}>/education-types' => 'enum/education-types',
    //    'GET <lang:\w{2}>/family-statuses' => 'enum/family-statuses',
    //    'GET <lang:\w{2}>/rates' => 'enum/rates',
    //    'GET <lang:\w{2}>/topic-types' => 'enum/topic-types',
    //    'GET <lang:\w{2}>/yesno' => 'enum/yesno',
    /* Enums */
];

return array_merge($controllerRoutes, $routes);
