
let handleLoginSubmit = async (e) => {
    e.preventDefault();
    let formData = new FormData(e.srcElement);
    let call_url = "api/users/userlogin.php";
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
    function updateWebpage(response) {
        if (response.error) {
            displayMessage(response.error.msg, 5000);
        } else {
            displayMessage("User successfully logged in!");
            sessionStorage.setItem("nickname", response.nickname);
            sessionStorage.setItem("token", response.token);
            setTimeout(window.location.href = "index.php", 500);
        }
        console.log(response);
    }
};
window.onload = onAssetsLoaded();
function onAssetsLoaded() {
    document.getElementById("loginForm").addEventListener("submit", handleLoginSubmit);
}