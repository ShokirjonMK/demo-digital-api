<?php

$controllers = [
    'get-teacher',
    'student',
    'employee',
    'user',
    'department',
    'job',
    'nationality',
    'languages',
    'residence-type',
    'science-degree',
    'scientific-title',
    'special-title',
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
    'student-exam',
    'country',

    'time-table',
    'time-option',
    'student-time-table',
    'student-time-option',

    'exam',
    'exam-student',
    'exam-question',
    'exam-question-type',
    'exam-question-option',
    'exam-student-answer',
    'exam-teacher-check',

    'subject',
    'subject-type',
    'subject-category',
    'subject-sillabus',
    'subject-access',
    'subject-topic',
    'subject-content',
    'subject-evaluation',

    'question',
    'question-type',
    'question-option',
    'exam-semeta',

    'user-access-type',
    'user-access',

    'citizenship',
    'notification',
    'notification-role',
    'nationality',
    'category-of-cohabitant',
    'residence-status',
    'social-category',
    'student-category',



    'teacher-checking-type',
    'statistic',

    'exam-checking',
    'exam-appeal',

    'survey-question',
    'survey-answer',
    'election',
    'election-candidate',
    'election-vote',
    'kpi-category',
    'kpi-store',
    'kpi-data',


    'partiya',
    'diploma-type',
    'degree',
    'degree-info',
    'academic-degree',

    'vocation',
    'vocation-type',
    'holiday',
    'job-title',
    'work-rate',

    'table-store',
    'instruction',
    'exam-appeal-semeta',

    'student-order',
    'order-type',
    'relative-info',
    'other-certificate',
    'other-certificate-type',
    'olympic-certificate',
    'sport-certificate',
    'lang-certificate',
    'lang-certificate-type',
    'military',
    'cantract',

    'subject-content-mark',
    'kpi-mark',
    'subject-topic-reference',
    'hostel-category',
    'hostel-category-type',
    'hostel-app',
    'hostel-doc',

    'teacher-content',
    'student-subject-selection',

    'club-category',
    'club',
    'club-time',
    'student-club',

    'attend',
    'attend-reason',
    'student-attend',

    'exam-control',
    'exam-control-student',

    'student-subject-restrict',
    'student-gpa-old',
    'hostel-student',

    'test-get-data',
    'teacher-work-plan',
    'tourniquet-absent',

    'student-mark',

    'poll',
    'poll-question',
    'poll-question-option',
    'poll-user',

    'login-history',
    'kpi-staff',
    'intensive',

    'scientific-specialization',
    'scientific-degree-document',

    'basis-for-publication',
    'monograph-brochure',
    'article',
    'tech-issue-type',
    'tech-issue',
    'user-appeal',
    'oferta',
    'visitor',


    'student-service',
    'student-service-type',

    'turniket',
    'open',

    'telegram',
    'test',

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
    /** telegram */
    'GET <lang:\w{2}>/telegrams/bot/<id:\d+>?' => 'telegram/bot',
    'GET <lang:\w{2}>/bot/<id:\d+>?' => 'telegram/bot',
    'GET /bot/<id:\d+>?' => 'telegram/bot',
    'GET /bot' => 'telegram/bot',
    'POST /bot' => 'telegram/bot-post',


    /** Turniket */
    'GET <lang:\w{2}>/turnikets/get' => 'turniket/get',
    'POST <lang:\w{2}>/turnikets/add' => 'turniket/add',
    'POST <lang:\w{2}>/turnikets/event' => 'turniket/event',
    'POST <lang:\w{2}>/opens/turniket' => 'open/turniket',
    'POST <lang:\w{2}>/opens/get-personal-data' => 'open/get-personal-data',
    /** Turniket */

    /** MIP pinfl */
    'GET <lang:\w{2}>/visitors/get/' => 'visitor/get',
    'POST <lang:\w{2}>/visitors/turniket/<id:\d+>?' => 'visitor/turniket',

    /** MIP pinfl */
    'GET <lang:\w{2}>/users/get/' => 'user/get',
    /** Oferta */
    'POST <lang:\w{2}>/users/oferta/' => 'user/oferta',
    /** not come */
    'GET <lang:\w{2}>/users/not-come/' => 'user/not-come',


    /** Scientific */
    // monograph-brochure
    'GET <lang:\w{2}>/monograph-brochures/types' => 'monograph-brochure/types',

    // Article Extra fields, type
    'GET <lang:\w{2}>/articles/extra' => 'article/extra',

    // user-appeal Extra fields, type
    'GET <lang:\w{2}>/user-appeals/extra' => 'user-appeal/extra',

    /** Scientific */

    /** Student from Hemis via pinfl */
    'GET <lang:\w{2}>/students/get/' => 'student/get',
    /** Student For turniket */
    'GET <lang:\w{2}>/students/by-pinfl/<pinfl>' => 'student/by-pinfl',
    'GET <lang:\w{2}>/students/time-option-not/' => 'student/time-option-not',

    /** Kpi staff */
    'GET <lang:\w{2}>/kpi-staff/self' => 'kpi-staff/self',
    'POST <lang:\w{2}>/kpi-staff/self/<id:\d+>?' => 'kpi-staff/self-post',
    'POST <lang:\w{2}>/kpi-staff/work-file/<id:\d+>?' => 'kpi-staff/work-file',
    'POST <lang:\w{2}>/kpi-staff/monitoring/<id:\d+>?' => 'kpi-staff/monitoring',
    'POST <lang:\w{2}>/kpi-staff/comission/<id:\d+>?' => 'kpi-staff/comission',
    'POST <lang:\w{2}>/kpi-staff/rector/<id:\d+>?' => 'kpi-staff/rector',
    'POST <lang:\w{2}>/kpi-staff/dep-lead/<id:\d+>?' => 'kpi-staff/dep-lead',

    'GET <lang:\w{2}>/departments/kpi' => 'department/kpi',
    'GET <lang:\w{2}>/kafedras/kpi' => 'kafedra/kpi',
    'GET <lang:\w{2}>/faculties/kpi' => 'faculty/kpi',



    /** Hostel Yotoqxona */
    'POST <lang:\w{2}>/hostel-docs/check/<id>/' => 'hostel-doc/check',
    'GET <lang:\w{2}>/hostel-docs/not/<id>/' => 'hostel-doc/not',

    /** attend-reason  */
    'GET <lang:\w{2}>/attend-reasons/confirm/<id>/' => 'attend-reason/confirm',

    /** Code Correctors */
    'GET <lang:\w{2}>/exam-students/correct/<key>/' => 'exam-student/correct',
    'POST <lang:\w{2}>/subject-contents/order' => 'subject-content/order',
    /** Code Correctors */

    /** exam control    */
    'GET <lang:\w{2}>/exam-controls/not-create' => 'exam-control/not-create',

    /** exam control student   */
    'POST <lang:\w{2}>/exam-control-students/check/<id>' => 'exam-control-student/check',
    'POST <lang:\w{2}>/exam-control-students/appeal/<id>/' => 'exam-control-student/appeal',
    'POST <lang:\w{2}>/exam-control-students/change-ball-with-file/<id:\d+>?' => 'exam-control-student/change-ball-with-file',

    /* statistics all */
    // statistic student-count-by-faculty
    'GET <lang:\w{2}>/statistics/student-count-by-faculty' => 'statistic/student-count-by-faculty',
    'GET <lang:\w{2}>/statistics/kpi-content-store' => 'statistic/kpi-content-store',
    'GET <lang:\w{2}>/statistics/kpi-survey-store' => 'statistic/kpi-survey-store',
    'GET <lang:\w{2}>/statistics/control-appeal' => 'statistic/control-appeal',
    'GET <lang:\w{2}>/statistics/exam-appeal' => 'statistic/exam-appeal',
    'GET <lang:\w{2}>/statistics/exam-checking' => 'statistic/exam-checking',
    'GET <lang:\w{2}>/statistics/exam-checkingnew' => 'statistic/exam-checkingnew',
    'GET <lang:\w{2}>/statistics/exam-checkinguntil' => 'statistic/exam-checkinguntil',
    'GET <lang:\w{2}>/statistics/exam-checkinguntil-teacher' => 'statistic/exam-checkinguntil-teacher',
    'GET <lang:\w{2}>/statistics/exam-notdis' => 'statistic/exam-notdis',
    'GET <lang:\w{2}>/statistics/bmi-tekshirish' => 'statistic/bmi-tekshirish',
    //// StudentTimeTableChala
    'GET <lang:\w{2}>/statistics/student-time-table' => 'statistic/student-time-table',

    // statistic Kafedra Questions Teachers
    'GET <lang:\w{2}>/statistics/kafedra' => 'statistic/kafedra',
    'GET <lang:\w{2}>/statistics/checking' => 'statistic/checking',
    'GET <lang:\w{2}>/statistics/checking-chala' => 'statistic/checking-chala',
    'GET <lang:\w{2}>/statistics/exam-checking' => 'statistic/exam-checking',

    // exam_student act qilish
    'POST <lang:\w{2}>/exam-students/<id>/act' => 'exam-student/act',
    'POST <lang:\w{2}>/exam-students/<id>/reexam' => 'exam-student/reexam',

    // ball statistics two, three, four, five
    'GET <lang:\w{2}>/exam-students/ball' => 'exam-student/ball',
    // ball statistics appeal
    'GET <lang:\w{2}>/exam-appeals/ball' => 'exam-appeal/ball',
    /* statistics all */

    // election password generator
    'GET <lang:\w{2}>/elections/<id>/password' => 'election/password',

    // Question status update
    'PUT <lang:\w{2}>/questions/status-update/<id>' => 'question/status-update',
    // Question status list
    'GET <lang:\w{2}>/questions/status-list' => 'question/status-list',
    // Question student
    'GET <lang:\w{2}>/questions/student' => 'question/student',

    // KpiCategory Extra fields, term, tab, status
    'GET <lang:\w{2}>/kpi-categories/extra' => 'kpi-category/extra',



    // Login and get access_token from server
    'POST <lang:\w{2}>/auth/login' => 'auth/login',
    // User Self update data
    'PUT <lang:\w{2}>/users/self' => 'user/self',
    // User Get Self data
    'GET <lang:\w{2}>/users/self' => 'user/selfget',
    // Get me
    'GET <lang:\w{2}>/users/me' => 'user/me',
    // Deleted users
    'GET <lang:\w{2}>/users/deleted' => 'user/deleted',
    // Log out
    'POST <lang:\w{2}>/auth/logout' => 'user/logout',

    // TimeTable parent null
    'GET <lang:\w{2}>/time-tables/parent-null' => 'time-table/parent-null',
    // Timetable import qilish uchun
    'POST <lang:\w{2}>/time-tables/import' => 'time-table/import',
    'POST <lang:\w{2}>/time-tables/teacher/<id>' => 'time-table/add-teacher',
    'PUT <lang:\w{2}>/time-tables/teacher/<id>' => 'time-table/update-teacher',
    'DELETE <lang:\w{2}>/time-tables/teacher/<id>' => 'time-table/delete-teacher',

    // Exam Passwords
    'POST <lang:\w{2}>/exams/get-passwords' => 'exam/get-passwords',
    // Exam Passwords
    'POST <lang:\w{2}>/exams/generate-passwords' => 'exam/generate-passwords',
    // exam Distribution
    'GET <lang:\w{2}>/exams/<id>/distribution' => 'exam/distribution',
    // exam Appeal Distribution
    'GET <lang:\w{2}>/exams/<id>/appeal-distribution' => 'exam/appeal-distribution',
    // exam announced // natijani e'lon qilish
    'GET <lang:\w{2}>/exams/<id>/ad' => 'exam/ad',
    // supervisor nazoratchi biriktirish
    'POST <lang:\w{2}>/exams/supervisor' => 'exam/supervisor',
    'DELETE <lang:\w{2}>/exams/supervisor/<id>' => 'exam/supervisor-delete',

    // exam conclusion defaulter
    'GET <lang:\w{2}>/exams/conclution' => 'exam/conclution-get',
    'POST <lang:\w{2}>/exams/conclution' => 'exam/conclution',
    'PUT <lang:\w{2}>/exams/conclution/<id>' => 'exam/conclution-update',
    'DELETE <lang:\w{2}>/exams/conclution/<id>' => 'exam/conclution-delete',

    // Department type list
    'GET <lang:\w{2}>/departments/types' => 'department/types',


    // studentga savollarni random tushirish
    'POST <lang:\w{2}>/exam-student-answers/get-question' => 'exam-student-answer/get-question',
    // ExamStudentAnswer Appeal checking
    'PUT <lang:\w{2}>/exam-checkings/<id>/appeal' => 'exam-checking/appeal',

    // teacherga studentlarni random tushirish
    'POST <lang:\w{2}>/exam-teacher-check/random-students' => 'exam-teacher-check/random-students',

    // Subject Content Trash ( get Deleted Content)
    'GET <lang:\w{2}>/subject-contents/trash' => 'subject-content/trash',
    // Subject Content Delete from Trash ( get Deleted Content)  bazadan o'chirish
    'DELETE <lang:\w{2}>/subject-contents/trash/<id>' => 'subject-content/trash-delete',
    // Subject Content type list
    'GET <lang:\w{2}>/subject-contents/types' => 'subject-content/types',

    // Faculty UserAccess fakultitetga user biriktirish
    'POST <lang:\w{2}>/faculties/user-access' => 'faculty/user-access',
    // Kafedra UserAccess fakultitetga user biriktirish
    'POST <lang:\w{2}>/kafedras/user-access' => 'kafedra/user-access',
    // Department UserAccess fakultitetga user biriktirish
    'POST <lang:\w{2}>/departments/user-access' => 'department/user-access',


    /** Free teachers for time tables */
    'GET <lang:\w{2}>/teacher-accesses/free' => 'teacher-access/free',
    'POST <lang:\w{2}>/rooms/free' => 'room/free',
    /**  */


    /**Builduing Type list */
    'GET <lang:\w{2}>/buildings/type' => 'building/type',


    /**Poll Type list */
    'GET <lang:\w{2}>/polls/type' => 'poll/type',


    // Student Attendees By
    'GET <lang:\w{2}>/attends/not' => 'attend/not',

    // Student Attendees By
    'GET <lang:\w{2}>/student-attends/by-date' => 'student-attend/by-date',

    // Student Get me
    'GET <lang:\w{2}>/students/me' => 'student/me',
    // self updater
    'PUT <lang:\w{2}>/students/me/<id>' => 'student/update-me',
    // student deleted list
    'GET <lang:\w{2}>/students/deleted' => 'student/deleted',
    // Student Study Types
    'GET <lang:\w{2}>/students/study-types' => 'student/study-types',
    // Student Services
    'POST <lang:\w{2}>/student-services/respond/<id>' => 'student-service/respond',
    // Student Services My
    'GET <lang:\w{2}>/student-services/my' => 'student-service/my',


    // Student Import
    'POST <lang:\w{2}>/students/import' => 'student/import',

    // image updater
    'POST <lang:\w{2}>/students/image/<id>' => 'student/image',
    // Student Export
    'GET <lang:\w{2}>/students/export' => 'student/export',
    // 'POST <lang:\w{2}>/students/read' => 'student/read',

    // My Notifications
    'GET <lang:\w{2}>/notifications/my' => 'notification/my',
    // Notifications Status list
    'GET <lang:\w{2}>/notifications/status-list' => 'notification/status-list',
    // Notifications Approved (tasdiqlavoring)
    'PUT <lang:\w{2}>/notifications/approved/<id>' => 'notification/approved',

    // Roles and permissions endpoint
    'GET <lang:\w{2}>/roles' => 'access-control/roles', // Get roles list
    'GET <lang:\w{2}>/roles/<role>/permissions' => 'access-control/role-permissions', // Get role permissions
    'POST <lang:\w{2}>/roles' => 'access-control/create-role', // Create new role
    'PUT <lang:\w{2}>/roles' => 'access-control/update-role', // Update role
    'DELETE <lang:\w{2}>/roles/<role>' => 'access-control/delete-role', // Delete role
    'GET <lang:\w{2}>/permissions' => 'access-control/permissions', // Get permissions list
    // ***

    'GET <lang:\w{2}>/user-statuses' => 'user/status-list', // Get user statuses

    // Lohin history
    'GET <lang:\w{2}>/login-histories/self' => 'login-history/self',


    /* Enums */
    'GET <lang:\w{2}>/genders' => 'enum/genders',
    'GET <lang:\w{2}>/educations' => 'enum/educations',
    'GET <lang:\w{2}>/education-degrees' => 'enum/education-degrees',
    'GET <lang:\w{2}>/disability-groups' => 'enum/disability-groups',
    'GET <lang:\w{2}>/education-types' => 'enum/education-types',
    'GET <lang:\w{2}>/family-statuses' => 'enum/family-statuses',
    'GET <lang:\w{2}>/rates' => 'enum/rates',
    'GET <lang:\w{2}>/topic-types' => 'enum/topic-types',
    'GET <lang:\w{2}>/yesno' => 'enum/yesno',
    /* Enums */
];

return array_merge($controllerRoutes, $routes);
