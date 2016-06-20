var socket = io('ws://localhost:8090/');
socket.on('connect', function(){});
socket.on('event', function(data){});
socket.on('disconnect', function(){});