window.onload = onWindowLoaded();
function onWindowLoaded() {
    let url = new URL(window.location.href);
    if (url.searchParams.get("username_change_status") === "success")
    {
        window.history.replaceState({page: "profile_settings"}, "name_change", url.origin + url.pathname);
        displayMessage("Nickname successfully changed!", 2000);
    }


}
async function changeNickname()
{
    let nickname_element = document.getElementById("nickname");
    if (nickname_element.value.length >= 3 && nickname_element.value.length <= 16)
    {
        let urlParameters = "nickname=" + nickname_element.value;
        let call_url = "api/users/change_nickname.php?" + urlParameters;
        try
        {
            const response = await fetch(call_url,
                    {
                        method: "GET",
                        headers: {'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'}
                    });
            updateWebpage(await response.json());
        } catch (error)
        {
            console.log("Fetch failed: ", error);
        }
    } else
    {
        displayMessage("Minecraft username length can only be between 3 and 16", 3000);
    }
    function updateWebpage(response)
    {
        if (!response.error)
        {
            let url = new URL(window.location.href);
            url.searchParams.set("username_change_status", "success");
            window.location.href = url;
        } else
        {
            displayMessage(response.error.msg, 2000);
        }
    }
}
function saveSettings()
{
    changeNickname();
}

async function resetPassword()
{
    let call_url = "api/users/reset_password.php";
    try
    {
        const response = await fetch(call_url,
                {
                    method: "GET",
                    headers: {'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'}
                });
        updateWebpage(await response.json());
    } catch (error)
    {
        console.log("Fetch failed: ", error);
    }
    function updateWebpage(response)
    {
        if (!response.error)
        {
            displayMessage(response.msg, 2000);
        } else
        {
            displayMessage(response.error.msg, 2000);
        }
    }
}

