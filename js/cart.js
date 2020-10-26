/* <tr>
 <td>
 <div class="media">
 <div class="d-flex">
 <img src="img/cart.jpg" alt="">
 </div>
 <div class="media-body">
 <p>Minimalistic shop for multipurpose use</p>
 </div>
 </div>
 </td>
 <td>
 <h5>$360.00</h5>
 </td>
 <td>
 <div class="product_count">
 <input type="text" name="qty" id="sst" maxlength="12" value="1" title="Quantity:"
 class="input-text qty">
 <button onclick="var result = document.getElementById('sst'); var sst = result.value; if( !isNaN( sst )) result.value++;return false;"
 class="increase items-count" type="button"><i class="lnr lnr-chevron-up"></i></button>
 <button onclick="var result = document.getElementById('sst'); var sst = result.value; if( !isNaN( sst ) &amp;&amp; sst > 0 ) result.value--;return false;"
 class="reduced items-count" type="button"><i class="lnr lnr-chevron-down"></i></button>
 </div>
 </td>
 <td>
 <h5>$720.00</h5>
 </td>
 </tr> */


let items;
window.onload = onWindowLoaded();
function onWindowLoaded()
{
    loadBasket();
}
async function loadBasket() {
    let call_url = "api/orders/basket.php";
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
    if (!items || items.length == 0)
    {
        noItemsPresent();
    }

    function updateWebpage(response) {
        if (!response.error)
        {
            items = response.data.items;
            for (let i = 0; i < items.length; i++) {
                let item = items[i];
                let htmlString =
                        `<tr id="product_${ item.product_id }">
                <td>
                    <div class="media">
                        <div class="d-flex">
                            <img alt="product image" title="go to product page" onclick="window.location.href = 'single-product.php?product=${item.product_id}'" class="cart_image" src="img/products/${ item.product_id }.png" alt="">
                            <p class="product_name">${ item.name }<span class="ag_mobile_display">(${item.quantity})</span></p>
                            </div>
                        <div class="media-body">
                        <span onclick="removeItem(${item.product_id});" title="remove item" class="lnr lnr-cross ag_product_remove_cross"></span>
                        </div>
                    </div>
                </td>
                <td>
                    <h5>€${ item.unit_price }</h5>
                </td>
                <td>
                    <div class="product_count">
                        <input type="text" name="qty" id="product_${ item.product_id }_quantity" maxlength="12" value="${ item.quantity }" title="Quantity:"
                            class="input-text qty">
                        <button onclick="var result = document.getElementById('product_${ item.product_id }_quantity'); var sst = result.value; if( !isNaN( sst )) result.value++;updateItemTotals();return false;"
                            class="increase items-count" type="button"><i class="lnr lnr-chevron-up"></i></button>
                        <button onclick="var result = document.getElementById('product_${ item.product_id }_quantity'); var sst = result.value; if( !isNaN( sst ) &amp;&amp; sst > 0 ) result.value--;updateItemTotals();return false;"
                            class="reduced items-count" type="button"><i class="lnr lnr-chevron-down"></i></button>
                    </div>
                </td>
                <td>
                    <h5>€<span id='product_${item.product_id}_total_price'>${ (item.unit_price * item.quantity).toFixed(2) }</span></h5>
                </td>
            </tr>`;
                $("#cartlist").prepend(htmlString);
            }
            updateItemTotals();
        } else
        {
            displayMessage(response.error.msg, 2500);
        }
    }
}

$(document).on("keyup", '.input-text.qty', function (event) {
    updateItemTotals();
});
function updateItemTotals()
{
    let cartTotal = 0;
    for (let i = 0; i < items.length; i++) {
        let item = items[i];
        let quantity = document.getElementById("product_" + item.product_id + "_quantity").value;
        let newItemTotal = (quantity * item.unit_price).toFixed(2);
        if (newItemTotal < 999999.999)
        {
            document.getElementById("product_" + item.product_id + "_total_price").innerHTML = newItemTotal;
        }
        cartTotal += parseFloat(newItemTotal);
    }
    if (cartTotal > 999999)
    {
        displayMessage("Cart total cannot exceed €999'999!", 2000);
    } else
    {
        document.getElementById("total_price").innerHTML = cartTotal.toFixed(2);
    }
}
async function updateCart() {

    let error_msg = "";
    var toBeRemovedItems = [];
    for (let i = 0; i < items.length; i++) {
        let item = items[i];
        let newQuantity = document.getElementById("product_" + item.product_id + "_quantity").value;
        if (!(newQuantity >= 0))
        {
            error_msg += item.name + " quantity is not a valid integer!";
        } else if (newQuantity == 0)
        {
            //remove it
            let response = await updateProduct(item.product_id, newQuantity);
            if (!response.error)
            {
                document.getElementById("product_" + item.product_id).parentNode.removeChild(document.getElementById("product_" + item.product_id));
                toBeRemovedItems.push(item);
            } else
            {
                error_msg += response.error.msg + "<br>";
            }
        } else if (newQuantity !== item.quantity)
        {
            //modify quantity
            let response = await updateProduct(item.product_id, newQuantity);
            if (!response.error)
            {
                items[i].quantity = newQuantity;
            } else
            {
                error_msg += response.error.msg + "<br>";
            }
        }
    }
    if (error_msg.length === 0)
    {
        displayMessage("Cart has been updated successfully", 4500);
    } else
    {
        displayMessage("Cart has been updated<br>" + error_msg, 5000);
    }
    for (var i = 0; i < toBeRemovedItems.length; i++)
    {
        items.splice(items.indexOf(toBeRemovedItems[i]), 1);
    }
    if (items.length == 0)
    {
        noItemsPresent();
    }
    updateItemTotals();
    async function updateProduct(product_id, newQuantity)
    {
        let call_url = "api/products/update_order.php?product=" + product_id + "&quantity=" + newQuantity;
        try
        {
            const response = await fetch(call_url,
                    {
                        method: "GET",
                        headers: {'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'}
                    });

            return updateWebpage(await response.json());
        } catch (error)
        {
            console.log("Fetch failed: ", error);
            return error;
        }
    }
    function updateWebpage(response)
    {
        if (!response.error)
        {

        }
        return response;
    }
}
function clearCart() {
    for (var i = 0; i < items.length; i++)
    {
        let item = items[i];
        document.getElementById("product_" + item.product_id + "_quantity").value = 0;
    }
    updateCart();
}

function removeItem(product_id)
{
    document.getElementById("product_" + product_id + "_quantity").value = 0;
    updateCart();
}
function noItemsPresent()
{
    if (!$("#no_products").length)
    {
        $("#cartlist").prepend("<tr id='no_products'><td><h2><a class='ag-active-category' href='category.php'>You've not added any items yet!</a></h2></td><td></td><td></td><td></td></tr>");
        $(".checkout_button").addClass("disable_page_button");
        $(".checkout_button").prop("onclick", null);
        $(document).on("click", '.checkout_button', function (event) {
            event.preventDefault();
        });
    }
}

