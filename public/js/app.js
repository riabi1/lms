function updateCartDropdown(cartItems, cartSubTotal, cartCount) {
    var dropdownHtml = "";
    if (cartCount > 0 && cartItems.length > 0) {
        $.each(cartItems, function (index, item) {
            dropdownHtml += `
                <li class="media media-card border-bottom pb-2 mb-2" id="cart-item-${
                    item.id
                }">
                    <a href="/course/details/${item.id}/${
                item.slug
            }" class="media-img mr-3">
                        <img src="${
                            item.image
                                ? "/storage/upload/course_images/thumbnail/" +
                                  item.image
                                : "/images/no_image.jpg"
                        }"
                            alt="${item.name || "Unknown Course"}"
                            class="lazy rounded"
                            style="width: 60px; height: auto;"
                            loading="lazy"
                            onerror="this.src='/images/no_image.jpg'">
                    </a>
                    <div class="media-body">
                        <h5 class="fs-14 font-weight-bold">
                            <a href="/course/details/${item.id}/${item.slug}">${
                item.name
                    ? item.name.substring(0, 25) +
                      (item.name.length > 25 ? "..." : "")
                    : "N/A"
            }</a>
                        </h5>
                        <p class="text-muted fs-13 lh-18">${
                            item.instructor_name || "Unknown Instructor"
                        }</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-black font-weight-semi-bold fs-14">${parseFloat(
                                item.price || 0
                            ).toFixed(2)} TND</span>
                            <button type="button" class="btn btn-link text-danger fs-13 p-0 remove-from-cart" data-id="${
                                item.id
                            }">Remove</button>
                        </div>
                    </div>
                </li>
            `;
        });
        dropdownHtml += `
            <li class="media media-card border-top pt-2 mt-2">
                <div class="media-body fs-15">
                    <p class="text-black font-weight-bold lh-18">Total: <span class="cart-total" id="cartSubTotal">${parseFloat(
                        cartSubTotal
                    ).toFixed(2)} TND</span></p>
                </div>
            </li>
        `;
    } else {
        dropdownHtml = `
            <li class="media media-card">
                <div class="media-body fs-15 text-center">
                    <p class="text-muted lh-18">Your cart is empty</p>
                </div>
            </li>
        `;
    }
    dropdownHtml += `
        <li class="mt-3">
            <a href="${window.location.origin}/cart" class="btn theme-btn w-100 py-2">Go to Cart <i class="la la-arrow-right icon ml-1"></i></a>
        </li>
    `;
    $("#cartDropdown").html(dropdownHtml);
    $("#cart-count").text(cartCount);
}
