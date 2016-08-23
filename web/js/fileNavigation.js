$(document).ready(function() {

  var shiftKeyIsPressed = false;

  var scrollToDiff = function(type) {
    var
      scrollPosition = $(document).scrollTop(),
      nextPos = 0,
      currentPosition = 0,
      margin = 100
      ;
    $(".added, .deleted").each(function() {
        currentPosition = $(this).offset().top;
        if (
            (type == 'bottom' && currentPosition  > (scrollPosition + margin))
            || (type == 'top' && currentPosition  < (scrollPosition + margin))
        ) {
          nextPos = currentPosition;
          return false;
        }
    });
    if (nextPos > 0) {
      var position = nextPos - margin;
      $(document).scrollTop(position);
    }
  };

  var changeFile = function(selector) {
    var location = $(selector).click().first().attr('href');
    if (typeof location != 'undefined') {
      window.location = location;
    }
  };

  var changeStatus = function (selector) {
    $(selector).click();
  };

  var toggleDiffOnly = function () {
    var $lines = $('td[class="line "]');
    // .toggle() is unbeliveably slow
    if ($lines.first().is(':visible')) {
      $lines.parent().hide();
    } else {
      $lines.parent().show();
    }
  };

  var toggleHelp = function () {
    var $help = $('#shortcutsHelp');
    // keep the keypress handler and the list of keys help in the same file!
    if ($help.prop('tagName') == null) {
      $('<div/>', {
          'id':'shortcutsHelp',
          'html':'\
<h2>Keyboard Shourtcuts</h2>\
<kbd>U</kbd> = change to Todo<br>\
<kbd>I</kbd> = change to Invalid<br>\
<kbd>O</kbd> = change to Valid<br>\
<kbd>D</kbd> = show diffs only<br>\
<kbd>K</kbd> = previous change<br>\
<kbd>J</kbd> = next change<br>\
<kbd>H</kbd> = previous file<br>\
<kbd>L</kbd> = next file<br>\
          '
      }).appendTo('body');
    } else {
      $help.toggle();
    }
  };

  $(document)
    .keydown(function(e) {
      if (e.target.type == 'textarea')
      {
        return true;
      }
      var code = e.keyCode || e.which;
      switch (code) {
        case 16: //shift
          shiftKeyIsPressed = true;
          break;
        case 75: //k
          scrollToDiff('top');
          break;
        case 74: //j
          scrollToDiff('bottom');
          break;
        case 72: //h
          changeFile('a.previous');
          break;
        case 76: //l
          changeFile('a.next');
          break;
        case 73: //i
          //invalid file
          changeStatus('a.invalidate');
          break;
        case 79: //o
          //valid file
          changeStatus('a.validate');
          break;
        case 85: //u
          //todo
          changeStatus('a.todo');
          break;
        case 68: //d
          //show only diff
          toggleDiffOnly();
          break;
        case 191: //?
          //toggle shortcuts help
          toggleHelp();
          break;
      }
    })
    .keyup(function(e) {
      var code = e.keyCode || e.which
      if (code == '16') { //shift
        shiftKeyIsPressed = false;
      }
    })
  ;

  $('#window').mousewheel(function(event, delta, deltaX, deltaY) {
      if(shiftKeyIsPressed) {
        event.preventDefault();
        this.scrollLeft -= (delta * 120);
      }
    })
  ;
});
