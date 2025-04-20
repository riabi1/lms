 {{-- /// Start Wishlist Add Option // --}}
 <script type="text/javascript">
 
    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
        }
    })

    function addToWishList(course_id){

        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "/add-to-wishlist/"+course_id,

            success:function(data){
                
                  // Start Message 

            const Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 6000 
            })
            if ($.isEmptyObject(data.error)) {
                    
                    Toast.fire({
                    type: 'success', 
                    icon: 'success', 
                    title: data.success, 
                    })

            }else{
               
           Toast.fire({
                    type: 'error', 
                    icon: 'error', 
                    title: data.error, 
                    })
                }

              // End Message   

            }
        })

    }
   

 </script>  
 {{-- /// End Wishlist Add Option // --}}

  {{-- /// Start Load Wishlist Data // --}}
 <script type="text/javascript">

    function wishlist(){
        $.ajax({
            type: "GET",
            dataType: 'json',
            url: "/get-wishlist-course/",

            success:function(response){

                $('#wishQty').text(response.wishQty);

                var rows = ""
                $.each(response.wishlist, function(key, value){

            rows += `
                    <div class="col-lg-4 responsive-column-half">
            <div class="card card-item">
                <div class="card-image">
                    <a href="/course/details/${value.course.id}/${value.course.course_name_slug}" class="d-block">
                        <img class="card-img-top" src="/${value.course.course_image}" alt="Card image cap">
                    </a>
                  
                </div><!-- end card-image --> 

                <div class="card-body">
                    <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">${value.course.label}</h6>
                    <h5 class="card-title"><a href="/course/details/${value.course.id}/${value.course.course_name_slug}">${value.course.course_name}</a></h5> 

                    <div class="d-flex justify-content-between align-items-center">
                        
                        ${value.course.discount_price == null 
                        ?`<p class="card-price text-black font-weight-bold">$${value.course.selling_price}</p>`
                        :`<p class="card-price text-black font-weight-bold">$${value.course.discount_price} <span class="before-price font-weight-medium">$${value.course.selling_price}</span></p>`
                        } 
                       
                        <div class="icon-element icon-element-sm shadow-sm cursor-pointer" data-toggle="tooltip" data-placement="top" title="Remove from Wishlist" id="${value.id}" onclick="wishlistRemove(this.id)" ><i class="la la-heart"></i></div>
                    </div>
                </div> 
            </div> 
        </div> 
             `
                });
               $('#wishlist').html(rows); 

            }
        })
    }
    wishlist();


    /// WishList Remove Start  // 

    function wishlistRemove(id){
        $.ajax({
            type: "GET",
            dataType: 'json',
            url: "/wishlist-remove/"+id,

            success:function(data){
             wishlist();
                 // Start Message 

            const Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 6000 
            })
            if ($.isEmptyObject(data.error)) {
                    
                    Toast.fire({
                    type: 'success', 
                    icon: 'success', 
                    title: data.success, 
                    })

            }else{
               
           Toast.fire({
                    type: 'error', 
                    icon: 'error', 
                    title: data.error, 
                    })
                }

              // End Message   


            }
        })

    } 

    /// End WishList Remove // 


 </script>
  {{-- /// End Load Wishlist Data // --}}



    {{-- /// Start Add To Cart  // --}}
  <script type="text/javascript">

   function addToCart(courseId, courseName, instructorId, slug){
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: { 
                _token: '{{ csrf_token() }}',
                course_name: courseName,
                course_name_slug: slug,
                instructor: instructorId
            },

            url: "/cart/data/store/"+ courseId,
            success: function(data) {
                miniCart();

                 // Start Message 

            const Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000 
            })
            if ($.isEmptyObject(data.error)) {
                    
                    Toast.fire({
                    type: 'success', 
                    icon: 'success', 
                    title: data.success, 
                    })

            }else{
               
           Toast.fire({
                    type: 'error', 
                    icon: 'error', 
                    title: data.error, 
                    })
                }

              // End Message   
            } 
        });
   }

</script>
     {{-- /// End Add To Cart  // --}}



         {{-- /// Start Buy Now Button  // --}}
  <script type="text/javascript">
 
    function buyCourse(courseId, courseName, instructorId, slug){
         $.ajax({
             type: "POST",
             dataType: 'json',
             data: { 
                 _token: '{{ csrf_token() }}',
                 course_name: courseName,
                 course_name_slug: slug,
                 instructor: instructorId
             },
 
             url: "/buy/data/store/"+ courseId,

             success: function(data) {
                 miniCart();
 
                  // Start Message 
 
             const Toast = Swal.mixin({
                   toast: true,
                   position: 'top-end',
                   showConfirmButton: false,
                   timer: 3000 
             })
             if ($.isEmptyObject(data.error)) {
                     
                     Toast.fire({
                     type: 'success', 
                     icon: 'success', 
                     title: data.success, 
                     });

                     // Redirect to the checkout page 
                     window.location.href = '/checkout';
 
             }else{
                
            Toast.fire({
                     type: 'error', 
                     icon: 'error', 
                     title: data.error, 
                     })
                 }
 
               // End Message   
             } 
         });
    }
 
 </script>
      {{-- /// End Buy Now Button  // --}}


 {{-- /// Start Mini Cart  // --}}
  <script type="text/javascript">

    function miniCart(){
        $.ajax({
            type: 'GET',
            url: '/course/mini/cart',
            dataType: 'json',
            success:function(response){

                $('span[id="cartSubTotal"]').text(response.cartTotal);
                $('#cartQty').text(response.cartQty);

                var miniCart = ""
                  
                $.each(response.carts, function(key,value){
                    miniCart += `<li class="media media-card">
                            <a href="shopping-cart.html" class="media-img">
                                <img src="/${value.options.image}" alt="Cart image">
                            </a>
                            <div class="media-body">
                                <h5><a href="/course/details/${value.id}/${value.options.slug}"> ${value.name}</a></h5>
                                  
                                 <span class="d-block fs-14">$${value.price}</span>
                                 <a type="submit" id="${value.rowId}" onclick="miniCartRemove(this.id)"><i class="la la-times"></i> </a> 
                            </div>
                        </li> 
                        `  
                });
                $('#miniCart').html(miniCart);

            }
        })
    }
    miniCart();

    // Mini Cart Remove Start 
    function miniCartRemove(rowId){
        $.ajax({
            type: 'GET',
            url: '/minicart/course/remove/'+rowId,
            dataType: 'json',
            success:function(data){
            miniCart();
// Start Message 

const Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000 
            })
            if ($.isEmptyObject(data.error)) {
                    
                    Toast.fire({
                    type: 'success', 
                    icon: 'success', 
                    title: data.success, 
                    })

            }else{
               
           Toast.fire({
                    type: 'error', 
                    icon: 'error', 
                    title: data.error, 
                    })
                }

              // End Message   


            }
        })
    }

    // End Mini Cart Remove 

 </script>
{{-- /// End Mini Cart // --}}



 {{-- /// Start MyCart  // --}}
 <script type="text/javascript">

    function cart(){
        $.ajax({
            type: 'GET',
            url: '/get-cart-course',
            dataType: 'json',
            success:function(response){

                $('span[id="cartSubTotal"]').text(response.cartTotal);

                var rows = ""
                $.each(response.carts, function(key,value){
                    rows += `
                    <tr>
                    <th scope="row">
                        <div class="media media-card">
                            <a href="course-details.html" class="media-img mr-0">
                                <img src="/${value.options.image}" alt="Cart image">
                            </a>
                        </div>
                    </th>
                    <td>
                        <a href="/course/details/${value.id}/${value.options.slug}" class="text-black font-weight-semi-bold">${value.name}</a>
                         
                    </td>
                    <td>
                        <ul class="generic-list-item font-weight-semi-bold">
                            <li class="text-black lh-18">$${value.price}</li>
                            
                        </ul>
                    </td>
                     
                    <td>
                        <button type="button" class="icon-element icon-element-xs shadow-sm border-0" data-toggle="tooltip" data-placement="top"  id="${value.rowId}" onclick="cartRemove(this.id)">
                            <i class="la la-times"></i>
                        </button>
                    </td>
                </tr>
                `
                });

                $('#cartPage').html(rows);


            }
        })
    }
    cart();

     // My Cart Remove Start 
     function cartRemove(rowId){
        $.ajax({
            type: 'GET',
            url: '/cart-remove/'+rowId,
            dataType: 'json',

            success:function(data){
            miniCart();
            cart();
            couponCalculation(); 
// Start Message 

const Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000 
            })
            if ($.isEmptyObject(data.error)) {
                    
                    Toast.fire({
                    type: 'success', 
                    icon: 'success', 
                    title: data.success, 
                    })

            }else{
               
           Toast.fire({
                    type: 'error', 
                    icon: 'error', 
                    title: data.error, 
                    })
                }

              // End Message   


            }
        })
    }

    // End My Cart Remove 

</script>
{{-- /// End MyCart // --}}


 {{-- /// Apply Coupon Start  // --}}
 <script type="text/javascript">
    function applyCoupon(){
        var coupon_name = $('#coupon_name').val();
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {coupon_name:coupon_name},
            url: "/coupon-apply",

            success:function(data){
                couponCalculation(); 

                if (data.validity == true) {
                    $('#couponField').hide();
                }

// Start Message 

const Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000 
            })
            if ($.isEmptyObject(data.error)) {
                    
                    Toast.fire({
                    type: 'success', 
                    icon: 'success', 
                    title: data.success, 
                    })

            }else{
               
           Toast.fire({
                    type: 'error', 
                    icon: 'error', 
                    title: data.error, 
                    })
                }

              // End Message   


            }
        })
    }


    /// Start Coupon Calculation Method 
    function couponCalculation(){
        $.ajax({
            type: 'GET',
            url: "/coupon-calculation",
            dataType: 'json',

            success:function(data){
                if (data.total) {
                    $('#couponCalField').html(
                        `<h3 class="fs-18 font-weight-bold pb-3">Cart Totals</h3>
                <div class="divider"><span></span></div>
                <ul class="generic-list-item pb-4">
                    <li class="d-flex align-items-center justify-content-between font-weight-semi-bold">
                        <span class="text-black">Subtotal:$</span>
                        <span>$${data.total} </span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between font-weight-semi-bold">
                        <span class="text-black">Total:$</span>
                        <span> $${data.total}</span>
                    </li>
                </ul>`
                    )
                    
                }else {
                    $('#couponCalField').html(
                        `<h3 class="fs-18 font-weight-bold pb-3">Cart Totals</h3>
                <div class="divider"><span></span></div>
                <ul class="generic-list-item pb-4">
                    <li class="d-flex align-items-center justify-content-between font-weight-semi-bold">
                        <span class="text-black">Subtotal: </span>
                        <span>$${data.subtotal} </span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between font-weight-semi-bold">
                        <span class="text-black">Coupon Name : </span>
                        <span>${data.coupon_name} <button type="button" class="icon-element icon-element-xs shadow-sm border-0" data-toggle="tooltip" data-placement="top" onclick="couponRemove()" >
                            <i class="la la-times"></i>
                        </button></span>
                    </li>


                    <li class="d-flex align-items-center justify-content-between font-weight-semi-bold">
                        <span class="text-black">Coupon Discount:</span>
                        <span> $${data.discount_amount}</span>
                    </li>

                    <li class="d-flex align-items-center justify-content-between font-weight-semi-bold">
                        <span class="text-black">Grand Total:</span>
                        <span> $${data.total_amount}</span>
                    </li> 

                </ul>`
                    )

                }

            }
        })
    }

   couponCalculation(); 

</script>
{{-- /// End Apply Coupon  // --}}

<script type="text/javascript">
   function applyInsCoupon(){
        var coupon_name = $('#coupon_name').val();
        var course_id = $('#course_id').val();
        var instructor_id = $('#instrutor_id').val();
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {coupon_name:coupon_name,course_id:course_id,instructor_id:instructor_id},
            url: "/inscoupon-apply",

            success:function(data){
                couponCalculation(); 

                if (data.validity == true) {
                    $('#couponField').hide();
                }

// Start Message 

const Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000 
            })
            if ($.isEmptyObject(data.error)) {
                    
                    Toast.fire({
                    type: 'success', 
                    icon: 'success', 
                    title: data.success, 
                    })

            }else{
               
           Toast.fire({
                    type: 'error', 
                    icon: 'error', 
                    title: data.error, 
                    })
                }

              // End Message   


            }
        })
    }


</script>

 {{-- /// Remove Coupon Start  // --}}
 <script type="text/javascript">

    function couponRemove(){
        $.ajax({
            type: "GET",
            dataType: 'json',
            url: '/coupon-remove',

            success:function(data){
                couponCalculation(); 
                $('#couponField').show();

                // Start Message 

const Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000 
            })
            if ($.isEmptyObject(data.error)) {
                    
                    Toast.fire({
                    type: 'success', 
                    icon: 'success', 
                    title: data.success, 
                    })

            }else{
               
           Toast.fire({
                    type: 'error', 
                    icon: 'error', 
                    title: data.error, 
                    })
                }

              // End Message   

            }
        })
    }


</script>
{{-- /// End Remove Coupon  // --}}



<script>
$(document).ready(function() {
    // Sync temp cart on page load for authenticated users
    @auth
        var tempCart = JSON.parse(localStorage.getItem('tempCart')) || [];
        if (tempCart.length > 0) {
            $.ajax({
                url: '{{ route("cart.sync") }}',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    tempCart: tempCart
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Temp cart sync success:', response);
                    if (response.success) {
                        // Clear localStorage
                        localStorage.removeItem('tempCart');
                        // Update cart UI
                        if ($('#cartQty').length) {
                            $('#cartQty').text(response.cartCount);
                        }
                        if ($('#cartSubTotal').length) {
                            $('#cartSubTotal').text('TND ' + response.cartSubTotal);
                        }
                        // Refresh cart dropdown
                        $.ajax({
                            url: '{{ route("cart") }}',
                            method: 'GET',
                            success: function(html) {
                                var $newCart = $(html).find('#cartDropdown').html();
                                $('#cartDropdown').html($newCart);
                                bindCartDropdownHandlers();
                            },
                            error: function(xhr) {
                                console.error('Cart dropdown refresh error:', xhr);
                            }
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Temp cart sync error:', xhr);
                }
            });
        }
    @endauth

    // Remove-from-cart handler
    $('.remove-from-cart').on('click', function(e) {
        e.preventDefault();
        var courseId = $(this).data('id');
        var cartRow = $('#cart-row-' + courseId); // Cart page row
        var cartItem = $('#cart-item-' + courseId); // Header dropdown item

        $.ajax({
            url: '{{ route("cart.remove", ":id") }}'.replace(':id', courseId),
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Remove item from cart page or header
                    if (cartRow.length) {
                        cartRow.remove();
                    }
                    if (cartItem.length) {
                        cartItem.remove();
                    }

                    // Update cart count
                    if ($('#cartQty').length) {
                        $('#cartQty').text(response.cartCount);
                    }

                    // Update cart page elements if present
                    if ($('#subtotal').length && $('#total-price').length) {
                        $('#subtotal').text(response.subtotal + ' TND');
                        $('#total-price').text(response.totalPrice + ' TND');

                        if (response.cartCount === 0) {
                            $('#cart-items').html('<tr><td colspan="4" class="text-center">Cart is empty.</td></tr>');
                            $('#cart-summary').remove();
                            $('#coupon-list').remove();
                        } else if (response.couponDiscount > 0) {
                            if (!document.getElementById('coupon-discount')) {
                                $('#subtotal').after('<p id="coupon-discount-container">Total Coupon Discount: <span id="coupon-discount">-' + response.couponDiscount + ' TND</span></p>');
                            } else {
                                $('#coupon-discount').text('-' + response.couponDiscount + ' TND');
                            }
                        } else {
                            $('#coupon-list').remove();
                            $('#coupon-discount-container').remove();
                        }
                    }

                    // Update header dropdown
                    if ($('#cartDropdown').length) {
                        if (response.cartCount === 0) {
                            $('#cartDropdown').html(
                                '<li class="media media-card">' +
                                '<div class="media-body fs-15 text-center">' +
                                '<p class="text-muted lh-18">Your cart is empty</p>' +
                                '</div></li>' +
                                '<li class="mt-3">' +
                                '<a href="{{ route('cart') }}" class="btn theme-btn w-100 py-2">Go to Cart <i class="la la-arrow-right icon ml-1"></i></a>' +
                                '</li>'
                            );
                        } else {
                            $('#cartSubTotal').text('TND ' + response.subtotal);
                        }
                    }

                    alert(response.message);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('An error occurred while removing the item.');
                console.error(xhr);
            }
        });
    });

    // Function to bind remove-from-cart handlers in cart dropdown
    function bindCartDropdownHandlers() {
        $('#cartDropdown .remove-from-cart').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('Remove from cart button clicked in dropdown');
            var courseId = $(this).data('id');
            var $cartItem = $('#cart-item-' + courseId);
            var $message = $('#cart-message-' + courseId).length ? $('#cart-message-' + courseId) : $('<div class="cart-message"></div>').appendTo('body');

            $.ajax({
                url: '{{ route("cart.remove", ":id") }}'.replace(':id', courseId),
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Remove from cart AJAX success:', response);
                    if (response.redirect) {
                        $message.html('<div class="alert alert-info">Please log in to remove this course from your cart.</div>');
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1500);
                    } else if (response.success) {
                        $cartItem.remove();
                        $message.html('<div class="alert alert-success">' + response.message + '</div>');
                        if ($('#cartQty').length) {
                            $('#cartQty').text(response.cartCount);
                        }
                        if ($('#cartSubTotal').length) {
                            $('#cartSubTotal').text('TND ' + response.cartSubTotal);
                        }
                        if (response.cartCount === 0) {
                            $('#cartDropdown').html(
                                '<li class="media media-card">' +
                                '<div class="media-body fs-15 text-center">' +
                                '<p class="text-muted lh-18">Your cart is empty</p>' +
                                '</div></li>' +
                                '<li class="mt-3">' +
                                '<a href="{{ route('cart') }}" class="btn theme-btn w-100 py-2">Go to Cart <i class="la la-arrow-right icon ml-1"></i></a>' +
                                '</li>'
                            );
                        }
                        // Update course card button state
                        $('.add-to-cart[data-course-id="' + courseId + '"]').each(function() {
                            $(this).data('in-cart', false).removeAttr('data-in-cart')
                                .prop('disabled', false)
                                .html('<i class="la la-shopping-cart fs-18 mr-1"></i> Add to Cart');
                        });
                    } else {
                        $message.html('<div class="alert alert-info">' + (response.message || 'Action completed.') + '</div>');
                    }
                    setTimeout(function() { $message.empty(); }, 3000);
                },
                error: function(xhr) {
                    console.error('Remove from cart AJAX error:', xhr);
                    var response = xhr.responseJSON || {};
                    $message.html('<div class="alert alert-danger">' + (response.message || 'An error occurred.') + '</div>');
                    setTimeout(function() { $message.empty(); }, 3000);
                }
            });
        });
    }
});
</script>
