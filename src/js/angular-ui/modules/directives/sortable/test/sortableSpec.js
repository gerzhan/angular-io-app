// Generated by CoffeeScript 1.3.3
(function() {

  describe('uiSortable', function() {
    beforeEach(module('ui.directives'));
    return describe('simple use', function() {
      it('should have a ui-sortable class', function() {
        return inject(function($compile, $rootScope) {
          var element;
          element = $compile("<ul ui-sortable></ul>")($rootScope);
          return expect(element.hasClass("ui-sortable")).toBeTruthy();
        });
      });
      return it('should update model when order changes', function() {
        return inject(function($compile, $rootScope) {
          var element;
          element = $compile('<ul ui-sortable ng-model="items"><li ng-repeat="item in items" id="s-{{$index}}">{{ item }}</li></ul>')($rootScope);
          $rootScope.$apply(function() {
            return $rootScope.items = ["One", "Two", "Three"];
          });
          return element.find('li:eq(1)').insertAfter(element.find('li:eq(2)'));
        });
      });
    });
  });

}).call(this);
