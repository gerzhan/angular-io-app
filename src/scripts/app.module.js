
// io settings
angular.module('io.config', []).value('io.config', {});

angular.module('io.controllers', [

	'io.controller.company',
	'io.controller.confirm',
	'io.controller.onboard',
	'io.controller.page',
	'io.controller.reset',
	'io.controller.sign',
	'io.controller.user',
	'io.controller.password'
]);
angular.module('io.directives', ['io.config', 'io.directive.htmlExtend', 'io.directive.inputMask', 'io.directive.markdown']);
angular.module('io.filters', ['io.config', 'io.filter.format', 'io.filter.range', 'io.filter.truncate']);
angular.module('io.factories', ['io.config', 'io.factory.accessibility']);
angular.module('io.init', ['io.config', 'io.init.settings', 'io.init.rootScope']);
angular.module('io', ['io.directives', 'io.filters', 'io.factories', 'io.init', 'io.config']);

// app settings
angular.module('app.config', []).value('app.config', {});

angular.module('app.controllers', [
	
	// io plugins
	'io.controller.contact',
	'io.controller.filepicker',
	'io.controller.follow',
	
	// app
	'io.controller.root',
	'app.controller.dashboard'
]);

angular.module('app.directives', ['app.config', 'ui.directives.if']);
angular.module('app.filters', ['app.config']);
angular.module('app.factories', ['app.config', 'io.factory.filepicker', 'io.factory.message', 'io.factory.follow']);

angular.module('app.init', ['app.config', 'app.route']);

var app = angular.module('app', ['ngCookies', 'io', 'app.directives', 'app.filters', 'app.factories', 'app.init', 'app.config']);