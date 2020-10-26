let max_page = 1;
let users_per_page = 25;
let url = new URL(window.location.href);
window.onload = onWindowLoaded();
function onWindowLoaded()
{
    displayUsers();
}
async function displayUsers()
{
    let current_page = 0;
//    url = new URL(window.location.href);
    let urlParams = (url).searchParams;
    if (urlParams.get("page"))
    {
        current_page = urlParams.get("page");
    }
    let searchString = "";
    let call_url = "api/users/list.php?page=" + current_page + "&search=" + searchString;
    try
    {
        const response = await fetch(call_url,
                {
                    method: "GET",
                    headers: {'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'}
                });
        // console.log(await response.text());
        updateWebpage(await response.json());
    } catch (error)
    {
        //clearUsers();
        generateNavigation();
        console.log("Fetch failed: ", error);
    }
    /* use the fetched data to change the content of the webpage */
    function updateWebpage(response)
    {
        console.log(response);
        let usersString = "";
        clearUsers();
        if (response.data) {
            for (let i = 0; i < response.data.users.length; i++)
            {
                usersString += `<li class="users_row">
                                    <div class="users_column">${response.data.users[i].mc_username != null ? response.data.users[i].mc_username : "[ USERNAME NOT SET ]"}</div>
                                    <div class="users_column">${response.data.users[i].email}</div>
                                    <div class="users_column">${response.data.users[i].user_id}</div>
                                    <div class="users_column users_cash_spent">${response.data.users[i].spent != null ? response.data.users[i].spent : 0}</div>
                                    <div class="users_column users_context_option">
                                        <span data-userid="${response.data.users[i].id}" class="users_context_click" onClick="openUserManageMenu(event)">&#x22EE;</span>
                                    </div>
                                </li>`;
            }
            max_page = Math.ceil(response.data.max_users / users_per_page);
        } else {
            max_page = 1;
        }
        generateNavigation();
        document.getElementById("users").innerHTML += usersString;
    }
    function generateNavigation() {
        //this section builds up the navigation panels
        let pages_string = "<span onClick='goNewPage(0, true)'>First</span>";
        let page_start = current_page - 2; //to keep the current page always in the middle of selection
        /* If the current page is at the start then set it to start 
         * from very first page, not keeping current page in the middle of selection*/
        if (page_start < 0) {
            page_start = 0;
        } else if (current_page > max_page - 3) {
            page_start = current_page - 3;
            if (current_page > max_page - 2) {
                page_start = current_page - 4;
            }
        } else {
            page_start = current_page - 2;
        }
        let iterate_for = page_start + 5;
        /* If the page is less than the set iteration then just iterate
         *  for that amount (e.g if max page 2 then only do 2 pages and don't go further*/
        if (max_page < iterate_for)
        {
            iterate_for = max_page;
        }
        /*Iterate starting from where the page starts.*/
        for (let j = page_start; j < iterate_for; j++)
        {
            if (current_page == j)
            {
                pages_string += `<span onclick="goNewPage(${j}, true)" class="active">${(j + 1)}</span>`;
            } else
            {
                pages_string += `<span onclick="goNewPage(${j}, true)">${(j + 1)}</span>`;
            }
        }
        pages_string += `<span onClick='goNewPage(${max_page - 1}, true)'>Last</span>`;
        let pages_elements = document.getElementsByClassName("pagination");
        for (let i = 0; i < pages_elements.length; i++)
        {
            pages_elements[i].innerHTML = pages_string;
        }
    }
}
function goNewPage(pageNumber, saveState)
{
    if (pageNumber >= 0 && pageNumber < max_page)
    {
        url.searchParams.set("page", pageNumber);
//       window.location.href = url;
        displayUsers();
    }

    if (saveState)
    {
        window.history.pushState({page: "user_search"}, "user_search", url);
    } else
    {
        window.history.replaceState({page: "user_search"}, "user_search", url);
    }
}
function clearUsers() {
    let users_container = document.getElementById("users");
    while (users_container.childNodes.length > 2) {
        users_container.removeChild(users_container.lastChild);
    }
}
function openUserManageMenu(e) {
    console.log("opening user manage menu");
    let element = document.getElementById("context_menu");
    element.setAttribute("data-userid", e.target.getAttribute("data-userid"));
    e.target.parentElement.appendChild(element);
    element.classList.remove("hide");
}
function closeUserManageMenu() {
    console.log("closing user manage menu");
    let element = document.getElementById("context_menu");
    element.setAttribute("data-userid", "");
    element.classList.add("hide");
}
function handleContextResetMcUsername(e) {
    let userid = e.target.parentElement.getAttribute("data-userid");
    closeUserManageMenu();
    resetMcUsername(userid);
}
async function resetMcUsername(userid) {
    let call_url = "api/users/reset_user_mc.php";
    try
    {
        const response = await fetch(call_url,
                {
                    method: "POST",
                    headers: {'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                    body: {userid: userid}
                });

        updateWebpage(await response.json());
    } catch (error)
    {
        displayMessage("Failed to delete user, try again later.", 3000);
        console.log("Fetch failed: ", error);
    }
    function updateWebpage(response) {
        if (!response.error) {
            displayUsers();
            displayMessage("User username reset.");
        } else {
            displayMessage(response.error.msg, 4000);
        }
    }
}
