// Generated by CoffeeScript 1.3.3
(function() {
  var exec, path, util;

  util = require('util');

  exec = require('child_process').exec;

  path = require('path');

  exports['default example project is generated'] = function(test) {
    test.expect(1);
    return exec('./bin/create', function(error, stdout, stderr) {
      if (error == null) {
        test.ok(true, "this assertion should pass");
      }
      return test.done();
    });
  };

  exports['default example project has a /phonegap folder'] = function(test) {
    test.expect(1);
    return path.exists('./example/phonegap', function(exists) {
      test.ok(exists, 'the other phonegap folder exists');
      return test.done();
    });
  };

}).call(this);