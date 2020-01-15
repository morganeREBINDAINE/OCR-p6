require("../scss/app.scss");
require("./homepage.js");
require("./single.js");
require("./forms.js");
require("./edit-form.js");
require("./create-form.js");
require("./user-form.js");

document.addEventListener("DOMContentLoaded", function (evt) {
    if (document.body.offsetHeight < window.innerHeight) {
        document.querySelector("footer").classList.add("stickybottom");
    }
});