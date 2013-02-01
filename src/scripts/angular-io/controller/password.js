//angular.module('io.controller.password', [])
//.controller('PasswordCtrl', ['$scope', '$http', function($scope, $http) {
PasswordCtrl.$inject = ['$scope', '$http'];
function PasswordCtrl($scope, $http) {
	console.log('PasswordCtrl ('+$scope.$id+')');
	
	$scope.errors = {};
	
	$scope.updatePassword = function() {
		$http.put($scope.settings.server+'account/password_change/', $scope.password)
			.success(function(data) {
				console.log(data);
				$scope.errors			= (data.errors) ? data.errors : {};
				$rootScope.alerts 		= (data.alerts) ? data.alerts : [];
				if (!data.alerts && !data.errors) {
					$scope.password = {};
					$rootScope.alerts = [{'class':'success', 'label':'Change Password:', 'message':'Saved'}];
				}
			})
			.error(function() {
				$rootScope.http_error();
			});
	};
	
	$scope.resetPassword = function(email) {
		console.log('reset_password()');
		$http.get($scope.settings.server+'account/reset_email/'+encodeURIComponent(email))
			.success(function(data) {
				console.log('reset_password.get.success');
				console.log(data);
				$scope.errors = (data.errors) ? data.errors : {};
				$rootScope.alerts = (data.alerts) ? data.alerts : [];
				$rootScope.alerts = [{'class':'info', 'message':'We have sent an email to '+email+' with further instructions.'}]; // replace in {{signin.email}}
			})
			.error(function() {
				console.log('reset_password.get.error');
				$rootScope.http_error();
			});
	};
}
//}]);