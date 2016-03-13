<?php

$app->post('/users',
  function() {
    //AuthController::checkAdminAuthorization();
  },
  array('UserController', 'getUsers')
);
$app->post('/update-account', array('UserController', 'updateAccount'));

$app->post('/all-users-stats',
  function() {
    //AuthController::checkAdminAuthorization();
  },
  array('StatController', 'getAllUserStats')
);
$app->post('/add-user-stat',
  function() {
    //AuthController::checkAdminAuthorization();
  },
  array('StatController', 'addUserStat')
);
$app->post('/update-user-stat',
  function() {
    //AuthController::checkAdminAuthorization();
  },
  array('StatController', 'updateUserStat')
);
$app->post('/update-user-stat-goal',
  function() {
    //AuthController::checkAdminAuthorization();
  },
  array('StatController', 'updateUserStatGoal')
);
$app->post('/add-stat',
  function() {
    AuthController::checkAdminAuthorization();
  },
  array('StatController', 'addStat')
);
$app->post('/update-stat',
  function() {
    AuthController::checkAdminAuthorization();
  },
  array('StatController', 'updateStat')
);

$app->post('/login', array('AuthController', 'login'));
$app->post('/logout', array('AuthController', 'logout'));
$app->post('/register', array('AuthController', 'register'));
$app->post('/reset-password', array('AuthController', 'resetPassword'));

$app->post('/user-add',
	function() {
		AuthController::checkAdminAuthorization();
	},
	array('UserController', 'addUser')
);

//$app->post('/user-avatar',array('UserController', 'addUserAvatar'));

$app->post('/add-log', array('LogController', 'addLog'));

