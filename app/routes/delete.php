<?php

$app->delete('/delete-user-stat/:user_stat_id/:user_id',
	function() {
		AuthController::checkAuthorization('user_id', FALSE, FALSE);
	},
	array('StatController', 'deleteUserStat')
);

