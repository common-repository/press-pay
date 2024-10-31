var _account_id = sift_science.account_id;
var _session_id = sift_science.session_id;
var _user_id = sift_science.user_id;
var _api_key = sift_science.api_key;

var _sift = _sift || [];
_sift.push(['_setAccount', _account_id]);
_sift.push(['_setUserId', _user_id]);
_sift.push(['_setSessionId', _session_id]);
_sift.push(['_trackPageview']);
(function() {
  function ls() {
    var e = document.createElement('script');
    e.type = 'text/javascript';
    e.async = true;
    e.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.siftscience.com/s.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(e, s);
  } if (window.attachEvent) {
    window.attachEvent('onload', ls);
  } else {
    window.addEventListener('load', ls, false);
  };
})();
