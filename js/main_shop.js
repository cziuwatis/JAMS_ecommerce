let max_page = 1;
let url = new URL(window.location.href);
let nonLinearSlider = document.getElementById('price-range');
window.onload = onWindowLoaded();
function onWindowLoaded()
{
    loadNoUiSlider();
    displayProducts();
    displayCategories();
}
async function displayProducts()
{
    let category_id;
    let product_sorting;
    let product_min_price = null; //null for nouislider and setting value when initially loading
    let product_max_price = null; //null for nouislider and setting value when initially loading
    let shop_page_number = 0;
    let shop_products_per_page = document.getElementById("ag_products_per_page").value;
//    url = new URL(window.location.href);
    let urlParams = (url).searchParams;
    if (urlParams.get("pagenumber"))
    {
        shop_page_number = urlParams.get("pagenumber");
    }
    if (urlParams.get("pagelimit"))
    {
        shop_products_per_page = urlParams.get("pagelimit");
    }
    if (urlParams.get("minprice"))
    {
        product_min_price = urlParams.get("minprice");
    }
    if (urlParams.get("maxprice"))
    {
        product_max_price = urlParams.get("maxprice");
    }
    setTimeout(function () { //timeout to help jquery and other plugins to set up so noUiSlider call works // sets initial start positions for nouislider on page load
        nonLinearSlider.noUiSlider.set([product_min_price, product_max_price]);
    }, 300);
    if (urlParams.get("sorting"))
    {
        product_sorting = urlParams.get("sorting");
    }
    if (urlParams.get("category_id"))
    {
        category_id = urlParams.get("category_id");
    }
    let call_url = "php/ajax_get_all_products_on_page.php?pagenumber=" + shop_page_number + "&pagelimit=" + shop_products_per_page + "&minprice=" + product_min_price + "&maxprice=" + product_max_price + "&sorting=" + product_sorting + "&category_id=" + category_id + "/";   /* name of file to send request to */
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
    /* use the fetched data to change the content of the webpage */
    function updateWebpage(response)
    {
        let product_string = "";
        if (response.data.products.length > 0 && response.data.products[0] !== false)
        {
            for (let i = 0; i < response.data.products.length; i++)
            {
                product_string += '<!-- product --><div class="col-lg-4 col-md-6"><div class="single-product" style="opacity: 0" title="' + response.data.products[i].product_id + '"><img class="img-fluid" src="./img/products/' + response.data.products[i].product_id + '.png" alt=""><div class="product-details"><h6>' + response.data.products[i].name + '</h6><div class="price"><h6>' + response.data.products[i].unit_price + '€</h6></div><div class="prd-bottom"><a href="#" onclick="addToCart(' + response.data.products[i].product_id + ', 1)" class="social-info"><span class="ti-bag"></span><p class="hover-text">add to bag</p></a></div></div></div></div>';
                // CUSTOM IMAGE URLS: product_string += '<!-- product --><div class="col-lg-4 col-md-6"><div class="single-product" style="opacity: 0" title="' + response.data.products[i].product_id + '"><img class="img-fluid" src="' + response.data.products[i].image_url + '" alt=""><div class="product-details"><h6>' + response.data.products[i].name + '</h6><div class="price"><h6>' + response.data.products[i].unit_price + '€</h6></div><div class="prd-bottom"><a href="" class="social-info"><span class="ti-bag"></span><p class="hover-text">add to bag</p></a><a href="" class="social-info"><span class="lnr lnr-heart"></span><p class="hover-text">Wishlist</p></a><a href="" class="social-info"><span class="lnr lnr-sync"></span><p class="hover-text">compare</p></a><a href="" class="social-info"><span class="lnr lnr-move"></span><p class="hover-text">view more</p></a></div></div></div></div>';
            }
            max_page = Math.ceil(response.data.prod_count.count / shop_products_per_page);
        } else
        {
            product_string = "<h2>Oops! No products were found!</h2>";
            max_page = 1;
        }
        let pages_elements = document.getElementsByClassName("pagination");
        for (let i = 0; i < pages_elements.length; i++)
        {
            pages_elements[i].innerHTML = getPaginationString();
        }
//        document.getElementById("product_list").classList.add("show_nice");
        document.getElementById("product_list").innerHTML = product_string;
        let product_elements = document.getElementsByClassName("single-product");
        setTimeout(function () {
            for (let i = 0; i < product_elements.length; i++)
            {
                product_elements[i].classList.add("show_nice");
            }
        }, 200);
    }
    function getPaginationString()
    {
        let pages_string = "";
        let disable_class = "";
        if (shop_page_number <= 0)
        {
            disable_class = "disable_page_button";
        }
        pages_string = '<a href="" title="' + (parseInt(shop_page_number) - 1) + '" class="prev-arrow ag-page-selector-arrow ' + disable_class + '" style="margin-left: -1px" ><i class="fa fa-long-arrow-left" aria-hidden="true"></i></a>';
        let page_start = shop_page_number - 2; //to keep the current page always in the middle of selection
        /* If the current page is at the start then set it to start 
         * from very first page, not keeping current page in the middle of selection*/
        if (page_start < 0) {
            page_start = 0;
        } else {
            page_start = shop_page_number - 1;
        }
        let iterate_for = page_start + 3;
        /* If the page is less than the set iteration then just iterate
         *  for that amount (e.g if max page 2 then only do 2 pages and don't go further*/
        if (max_page < iterate_for)
        {
            iterate_for = max_page;
        }
        /* If current page number is greater than 1 (starting from 0) add the dots and move first page to left
         * so you can go back to first page from any page.*/
        if (shop_page_number > 1)
        {
            pages_string += '<a href="" class="page-btn" >1</a>';
            pages_string += '<a href="" class="dot-dot"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a>';
        }
        for (let j = page_start; j < iterate_for; j++)
        {
            if (shop_page_number == j) //== works but === doesn't. rip
            {
                pages_string += '<a href="" class="active">' + (j + 1) + '</a>';
            } else
            {
                pages_string += '<a href="" class="page-btn">' + (j + 1) + '</a>';
            }
        }
        if (max_page > 3 && shop_page_number < max_page - 2)
        {
            pages_string += '<a href="" class="dot-dot"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a>';
            pages_string += '<a href=""  class="page-btn" >' + max_page + '</a>';
        }
        disable_class = "";
        if (shop_page_number >= max_page - 1)
        {
            disable_class = "disable_page_button";
        }
        pages_string += '<a href="" title="' + (parseInt(shop_page_number) + 1) + '" class="next-arrow ag-page-selector-arrow ' + disable_class + '"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>';
        return pages_string;
    }
}
async function displayCategories()
{
    let call_url = "api/categories/list.php";
    let urlParameters = "";
    try
    {
        const response = await fetch(call_url,
                {
                    method: "POST",
                    headers: {'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                    body: urlParameters
                });

        updateWebpage(await response.json());
    } catch (error)
    {
        console.log("Fetch failed: ", error);
    }
    /* use the fetched data to change the content of the webpage */
    function updateWebpage(response)
    {
        let categories_string = "";
        let total_count = 0;
        if (response.data.categories.length > 0)
        {
            for (let i = 0; i < response.data.categories.length; i++)
            {
                let product_count = parseInt(response.data.categories_numbers[i].product_count);
                total_count += product_count;
                categories_string += '<li class="main-nav-list"><a class="ag-category-item" title="' + response.data.categories[i].category_id + '" onclick="categoryClick(this);setParam(\'category_id\', ' + response.data.categories[i].category_id + ')" data-toggle="collapse" href="" aria-expanded="false" aria-controls="' + response.data.categories[i].name + '"><spanclass="lnr lnr-arrow-right"></span>' + response.data.categories[i].name + '<span class="number">(' + product_count + ')</span></a></li>';
            }
        }
        let all_categories = '<li class="main-nav-list"><a class="ag-category-item" title="all categories" onclick="categoryClick(this);setParam(\'category_id\', null)" data-toggle="collapse" href="" aria-expanded="false" aria-controls="All categories"><spanclass="lnr lnr-arrow-right"></span>All categories<span class="number">(' + total_count + ')</span></a></li>';
        document.getElementById("ag_categories").innerHTML = all_categories + categories_string;
        categoryClick();
    }
}
$(document).on("click", '.disable_page_button', function (event) {
    event.preventDefault();
});
$(document).on("click", '.page-btn', function (event) {
    event.preventDefault();
    goNewPage(this.text - 1);
});
$(document).on("click", '.ag-page-selector-arrow', function (event) {
    event.preventDefault();
    goNewPage(parseInt(this.title));
});
$(document).on("click", '.single-product', function (event) {
    location.href = "single-product.php?product=" + this.title;
});
$(document).on("click", '.social-info', function (event) {
    event.stopPropagation();
});
function goNewPage(pageNumber, saveState)
{
    if (pageNumber >= 0 && pageNumber < max_page)
    {
        url.searchParams.set("pagenumber", pageNumber);
        let pageLimit = $("#ag_products_per_page").val();
        if (url.searchParams.get("pagelimit"))
        {
            pageLimit = url.searchParams.get("pagelimit");
        }
        url.searchParams.set("pagelimit", pageLimit);
//        window.location.href = url;
        displayProducts();
        //state object, title, url
        if (saveState)
        {
            window.history.pushState({page: "product_search"}, "user_product_search", url);
        } else
        {
            window.history.replaceState({page: "product_search"}, "user_product_search", url);
        }
    }
}
function setParam(param_name, param_value)
{
    if (url.searchParams.get(param_name) !== param_value)
    {
        url.searchParams.set(param_name, param_value);
        goNewPage(0);
    }
}
function setParams(param_name1, param_value1, param_name2, param_value2)
{
    if (url.searchParams.get(param_name1) !== param_value1 || url.searchParams.get(param_name2) !== param_value2)
    {
        url.searchParams.set(param_name1, param_value1);
        url.searchParams.set(param_name2, param_value2);
        goNewPage(0);
    }
}
//----- Active No ui slider --------//
function loadNoUiSlider()
{
    $(function () {

        if (document.getElementById("price-range")) {


            noUiSlider.create(nonLinearSlider, {
                connect: true,
                behaviour: 'tap',
                start: [0, 200],
                range: {
                    // Starting at 500, step the value by 500,
                    // until 4000 is reached. From there, step by 1000.
                    'min': [0],
                    '50%': [50, 5],
                    'max': [100]
                }
            });


            var nodes = [
                document.getElementById('lower-value'), // 0
                document.getElementById('upper-value')  // 1
            ];

            // Display the slider value and how far the handle moved
            // from the left edge of the slider.
            nonLinearSlider.noUiSlider.on('update', function (values, handle, unencoded, isTap, positions) {
                nodes[handle].innerHTML = values[handle];
            });
            //Update displayed products on price values change
            nonLinearSlider.noUiSlider.on('change', function (values) {
                updatePriceSearch(values);
            });
        }

        function updatePriceSearch(values)
        {
            let min_range = parseFloat(values[0]);
            let max_range = parseFloat(values[1]);
            setParams("minprice", min_range, "maxprice", max_range);
        }
    });
}
function categoryClick(clickedElement)
{
    var categories = document.getElementsByClassName("ag-category-item");
    var current_category_header_element = document.getElementById("current_category");
    for (let i = 0; i < categories.length; i++)
    {
        categories[i].classList.remove("ag-active-category");
    }
    if (clickedElement) {
        clickedElement.classList.add("ag-active-category");
        current_category_header_element.innerHTML = clickedElement.innerHTML;
    } else {
        //set active category from url
        for (let i = 0; i < categories.length; i++)
        {
            if (categories[i].title === url.searchParams.get("category_id"))
            {
                categories[i].classList.add("ag-active-category");
                current_category_header_element.innerHTML = categories[i].innerHTML;
            }
        }
    }
}


async function addToCart(product_id, quantity)
{
    let call_url = "api/products/order.php?product=" + product_id + "&quantity=" + quantity;
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
    /* use the fetched data to change the content of the webpage */
    function updateWebpage(response)
    {
        console.log(response);
        if (!response.error)
        {
            displayMessage(response.data.name + " has been succcesfully added to cart!", 2500);
        } else
        {
            displayMessage(response.error.msg, 3000);
        }
    }
}
