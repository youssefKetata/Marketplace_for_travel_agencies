import React, {useState, useEffect} from "react";
import WishlistCard from "./WishlistCard";

export default function WhishList() {
    const wishlist = JSON.parse(localStorage.getItem('wishlist'))
    const sellers = JSON.parse(localStorage.getItem('sellers'))
    const [whishlistHotels, setWhishlistHotels] = useState(wishlist)

    const removeHotel = (hotelId) => {
        const wl = JSON.parse(localStorage.getItem('wishlist'))
        const newWishlist = wl.filter(hotel => hotel.hotelId !== hotelId)
        localStorage.setItem('wishlist', JSON.stringify(newWishlist))
        setWhishlistHotels(newWishlist)
    }

    return (
        <div className='whishlist-hotels'>
            <div className='whichList-header'>Wich list </div>
            {!whishlistHotels  || whishlistHotels.length === 0 ? <div className='emptyWhichList-text'>Wishlist is empty</div> : whishlistHotels.map(hotel => {
                {
                    if (hotel.sellers.length >= 1) {
                        return (
                            <div key={hotel.hotelId} >

                                <WishlistCard key={`hotel-card-${hotel.hotelId}`}
                                              sellersInfo={sellers[0]}
                                              sellers={hotel.sellers}
                                              hotel={hotel}
                                              removeHotel={removeHotel}
                                />
                            </div>
                        );
                    }
                }

            })}
        </div>
    )
}