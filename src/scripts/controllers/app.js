//angular.module('app.controller.root', [])
//.controller('AppCtrl',
//['$rootScope', '$scope', '$rest', '$follow', '$filepicker',
//function(rootScope, $scope, $http, follow, filepicker) {
function AppCtrl($rootScope, $scope, $window, $cookies, $location, $session, $rest, $filepicker, $accessibility, $message, $follow) {
	console.log('AppCtrl (', $scope.$id, ')');

	// Route Events
	$rootScope.$on('$routeChangeError', function(event, current, previous, rejection) {
		console.error('Failed to change routes.' + rejection); // rejection from defer.reject();
	});
	$rootScope.$on('$routeChangeStart', function(event, current, previous, rejection) {

	});
	$rootScope.$on('$routeChangeSuccess', function(event, current, previous, rejection) {

	});
	// Render Events
	$rootScope.$on('$viewContentLoaded', function(event) {
		// ga - add $window and $location to $inject if adding in
		// use $rootScope.$on('$routeChangeSuccess', ...) or angular-googleanalytics
		//$window._gaq.push(['_trackPageView', $location.path()]);
	});
	$rootScope.$on('$includeContentLoaded', function(event) {

	});


	// Factory init - $scope.factory = factory;
	$rootScope.session = $session;
	$rootScope.filepicker = $filepicker;
	$rootScope.accessibility = $accessibility;
	$rootScope.message = $message;
	$rootScope.follow = $follow;
	/*
	markdown.setOptions({
		gfm: true,
		tables: true,
		breaks: false,
		pedantic: false,
		sanitize: true,
		smartLists: true
	});
	$rootScope.markdown = markdown;
	*/


	$scope.slideNavBool = -1;		// slider nav state (-1,+1)
	$scope.slideNav = function(value) {
		if (value === 1 || value === -1) { $scope.slideNavBool = value; }
		else { $scope.slideNavBool *= -1; }
	};
	//!-- App Root Scoope Functions --//
	/*
	$scope.sampleRequest = function(a, b, c, d) {
		$rest.http({
				method:'get', // get,head,post,put,delete,jsonp
				url: '/'+$scope.search.type+'/search/'+$scope.search.query,
				data:{}
			}, function(data){
				$scope.results = data;
			});
	};
	*/
}
AppCtrl.$inject = ['$rootScope', '$scope', '$window', '$cookies', '$location', '$session', '$rest', '$filepicker', '$accessibility', '$message', '$follow'];
//}]);
