var prevColor;
function getRandomColor_Calendar(usePrev) {
  if (usePrev && prevColor)
    return prevColor;

  var letters = '0123456789ABCDEF';
  var color = '#';
  for (var i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  prevColor = color;
  return color;
}