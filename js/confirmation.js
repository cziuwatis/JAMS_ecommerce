window.onload = onWindowLoaded();
function onWindowLoaded()
{
    getOrderId();
}
async function getOrderId()
{
    let session_id = new URL(window.location.href).searchParams.get("session_id");
    if (!session_id)
    {
        window.location.href = "index.php";
    }
    let call_url = "api/orders/retrieve_stripe_session.php?session_id=" + session_id;
    try
    {
        const response = await fetch(call_url,
                {
                    method: "GET",
                    headers: {'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'}
                });

        getProducts(await response.json());
    } catch (error)
    {
        console.log("Fetch failed: ", error);
    }
    async function getProducts(order_id)
    {
        if (!order_id.error)
        {
            let call_url = "api/orders/get.php?order=" + order_id;
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
            //error, order not found, user may not be allowed to view this order, session doesn't exist etc.
            window.location.href = "index.php";
        }
        function updateWebpage(response)
        {
            if (response.data)
            {
                let html_string = "";
                order_id;
                let order_info_element = document.getElementById("order_info");
                //order information
                html_string += '<li><a href="#"><span>Order id</span> : ' + order_id + '</a></li>';
                html_string += '<li><a href="#"><span>Date</span> : ' + response.data.date_ordered + '</a></li>';
                html_string += '<li><a href="#"><span>Payment method</span> : Card</a></li>';
                html_string += '<li><a href="#"><span>Minecraft username</span> : ' + response.data.mc_username + '</a></li>';
                order_info_element.innerHTML = html_string;
                html_string = "";
                //product information                
                let products_element = document.getElementById("product_list");
                let total = 0;
                for (var i = 0; i < response.data.items.length; i++)
                {
                    var item = response.data.items[i];
                    var price = item.unit_price * item.quantity;
                    total += price;
                    html_string += "<tr><td><p>" + item.name + "</p></td><td><h5>x " + item.quantity + "</h5></td><td><p>€" + price.toFixed(2) + "</p></td></tr>";
                }
                html_string += "<tr><td><p>Total</p></td><td></td><td><p>€" + total.toFixed(2) + "</p></td></tr>";
                products_element.innerHTML = html_string;
                order_info_element.innerHTML += '<li><a href="#"><span>Total</span> : EUR ' + total.toFixed(2) + '</a></li>';
            }
        }
    }
}