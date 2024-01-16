
function handleFocusEvent() {
    Languages.style.display = 'block';
    code.style.borderRadius = '5px 5px 0 0';
  handleLanguagesDisplay();
};

for (let option of Languages.options) {
    option.onclick = function () {
        code.value = option.value;
        Languages.style.display = 'none';
        code.style.borderRadius = '5px';
    }
};

code.oninput = function () {
    currentFocus = -1;
    var text = code.value.toUpperCase();
    var maxItems = 5; // عدد العناصر المراد عرضها
    var count = 0; // عداد لتتبع عدد العناصر المعروضة

    for (let option of Languages.options) {
        if (option.value.toUpperCase().indexOf(text) > -1) {
            if (count < maxItems) {
                option.style.display = 'block';
                count++;
            } else {
                option.style.display = 'none';
            }
        } else {
            option.style.display = 'none';
        }
    }
}
var currentFocus = -1;
code.onkeydown = function (e) {
    if (e.keyCode == 40) {
        currentFocus++
        addActive(Languages.options);
    }
    else if (e.keyCode == 38) {
        currentFocus--
        addActive(Languages.options);
    }
    else if (e.keyCode == 13) {
        e.preventDefault();
        if (currentFocus > -1) {
            /*and simulate a click on the 'active' item:*/
            if (Languages.options) Languages.options[currentFocus].click();
        }
    }
}

function addActive(x) {
    if (!x) return false;
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    x[currentFocus].classList.add('active');
}
function removeActive(x) {
    for (var i = 0; i < x.length; i++) {
        x[i].classList.remove('active');
    }
}