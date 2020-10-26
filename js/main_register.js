let url = new URL(window.location.href);
let handleRegistrationChange = (e) => {
    //console.log(e);
    let formData;
    if (e.srcElement.form) {
        formData = new FormData(e.srcElement.form);
    } else {
        formData = new FormData(e.srcElement);
    }
    let errors = validateRegistration(formData.get("email"), formData.get("password"), formData.get("confirm-password"));
    let error_container = document.getElementById("register_form_errors");
    error_container.classList.add("show");
    for (var i = 0; i < error_container.children.length; i++) {
        if (errors.includes(i)) {
            error_container.children[i].classList.add("register_incorrect_input");
        } else {
            error_container.children[i].classList.remove("register_incorrect_input");
        }
    }
    return errors;
};
let handleRegistrationSubmit = async (e) => {
    e.preventDefault();
    if (handleRegistrationChange(e).length < 1) {
        let formData = new FormData(e.srcElement);
        formData.set("token", sessionStorage.getItem("token"));
        let call_url = "api/users/userregister.php";
        let body = "";
        try
        {
            const response = await fetch(call_url,
                    {
                        method: "POST",
                        //headers: {'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                        body: formData
                    });
            body = await response.text();
            updateWebpage(JSON.parse(body));
        } catch (error)
        {
            console.log("Fetch failed: ", error);
            console.log(body);
        }
    } else {
        displayMessage("Not all fields have been filled out correctly", 5000);
    }
    function updateWebpage(response) {
        if (response.error) {
            displayMessage(response.error.msg, 5000);
        } else {
            displayMessage("User successfully registered!");
            sessionStorage.setItem("nickname", response.nickname);
            sessionStorage.setItem("token", response.token);
            setTimeout(window.location.href = "index.php", 500);
        }
    }
};
let handleResetChange = (e) => {
    //console.log(e);
    let formData;
    if (e.srcElement.form) {
        formData = new FormData(e.srcElement.form);
    } else {
        formData = new FormData(e.srcElement);
    }
    let errors = validateRegistration("", formData.get("password"), formData.get("confirm-password"));
    let error_container = document.getElementById("reset_form_errors");
    error_container.classList.add("show");
    for (var i = 1; i <= error_container.children.length; i++) {
        if (errors.includes(i)) {
            error_container.children[i - 1].classList.add("register_incorrect_input");
        } else {
            error_container.children[i - 1].classList.remove("register_incorrect_input");
        }
    }
    return errors;
};
let handleResetSubmit = async (e) => {
    e.preventDefault();
    if (handleResetChange(e).length < 2) {
        let formData = new FormData(e.srcElement);
        let call_url = "api/users/perform_password_reset.php";
        let urlParams = url.searchParams;
        formData.set("resetToken", urlParams.get("reset"));
        try
        {
            const response = await fetch(call_url,
                    {
                        method: "POST",
                        //headers: {'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                        body: formData
                    });
            updateWebpage(await response.json());
        } catch (error)
        {
            console.log("Fetch failed: ", error);
        }
    } else {
        displayMessage("Not all fields have been filled out correctly", 5000);
    }
    function updateWebpage(response) {
        if (response.error) {
            displayMessage(response.error.msg, 5000);
            setTimeout(function() {window.location.href = "index.php";}, 5000);
            
        } else {
            displayMessage("Password successfully reset!");
            sessionStorage.setItem("nickname", response.nickname);
            sessionStorage.setItem("token", response.token);
            setTimeout(window.location.href = "index.php", 500);
        }
    }
};
window.onload = onAssetsLoaded();
function onAssetsLoaded() {
    if (document.getElementById("registerForm") !== null) {
        document.getElementById("registerForm").addEventListener("keyup", handleRegistrationChange);
        document.getElementById("registerForm").addEventListener("submit", handleRegistrationSubmit);
    }
    if (document.getElementById("resetForm") !== null) {
        if (new URL(window.location.href).searchParams.get("reset")) {
            console.log(document.title);
            window.history.replaceState({}, document.title, window.location.pathname);
            document.getElementById("resetForm").addEventListener("keyup", handleResetChange);
            document.getElementById("resetForm").addEventListener("submit", handleResetSubmit);
        } else {
            window.location.href = "index.php";
        }
    }
}
function validateRegistration(email, password, confirmedPassword) {
    return validateEmail(email).concat(validatePassword(password).concat(validateConfirmedPassword(password, confirmedPassword)));
}
function validateEmail(email)
{
    let error = [];

    // valid email pattern
    const pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (!pattern.test(email.toLowerCase())) {
        error.push(0);
    }
    return error;
}

function validatePassword(password)
{
    let error = [];

    if (password.length < 10)
    {
        error.push(1);
    }
    if (!/[a-z]+/.test(password))
    {
        error.push(2);
    }
    if (!/[A-Z]+/.test(password))
    {
        error.push(3);
    }
    if (!/[0-9]+/.test(password))
    {
        error.push(4);
    }
    if (!/[£!#€$%^&*]+/.test(password))
    {
        error.push(5);
    }
    return error;
}

function validateConfirmedPassword(password, confirmedPassword)
{
    let error = [];
    if (password !== confirmedPassword) {
        error.push(6);
    }
    return error;
}

