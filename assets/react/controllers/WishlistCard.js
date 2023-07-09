import React, { useEffect, useState } from 'react'
import axios from 'axios'
import Carousel from 'react-bootstrap/Carousel'
import Dropdown from 'react-bootstrap/Dropdown';


export default function WishlistCard(props) {
    const NbvisibleSellers = 3;
    const sellersInfo = props.sellersInfo;
    const sellers = props.sellers;
    const hotel = props.hotel;
    const visibleSellers = props.sellers.slice(0, NbvisibleSellers);
    const hiddenSellers = props.sellers.slice(NbvisibleSellers);

    async function getUserLocation() {
        let loc = { 'ipAddress': null, 'city': null, 'country': null, 'continent': null };
        try {
            const response = await axios.get('https://api.ipify.org?format=json');
            loc.ipAddress = response.data.ip;

            // wait for the coords to be returned
            const { coords } = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject);
            });

            // Reverse geocode user's location using OpenCage Geocoder
            const geoResponse = await axios.get(`https://api.opencagedata.com/geocode/v1/json?q=${coords.latitude}+${coords.longitude}&key=1222294752a64323b9fb89442efd865c`);
            loc.continent = geoResponse.data.results[0].components.continent;
            loc.country = geoResponse.data.results[0].components.country;
            loc.city = geoResponse.data.results[0].components.city;

        } catch (error) {
            console.log(error);
        }
        return loc;
    }



    async function handleLinkClick(sellerId) {
        const productType = 'hotels';
        let userInfo = await getUserLocation();
        if (userInfo === null) {
            userInfo.ipAddress = '';
            userInfo.country = '';
            userInfo.city = '';
            userInfo.continent = '';
        }
        let data = {
            'sellerId': sellerId,
            'hotelId': sellers[0].hotelId,
            'hotelName': sellers[0].hotelName,
            'productType': productType,
            'userIP': userInfo.ipAddress,
            'userCountry': userInfo.country,
            'userContinent': userInfo.continent,
            'userCity': userInfo.city,
        }

        const response = await axios.post(`http://user1-market.3t.tn/search/searchClick/${JSON.stringify(data)}`)
            .then((response) => {
                    console.log('responseData: ', response.data);
                }
            ).catch((error) => {
                console.log('error: ', error);
            })

    }


    return (
        <div key={sellers[0].hotelId}>

            <section className="whishlist-main-content" key={sellers[0].hotelId}>
                <div className="whishlist-container" key={sellers[0].hotelId}>
                    <div className="row">
                        <div className="whishlist-hotel-card ">
                            <div className="whishlist-hotel-card_images">
                                <Carousel interval={null}>
                                    {sellers.map(seller => {
                                        return (
                                            <Carousel.Item key={`${seller.sellerId}` - `${seller.hotelId}`}>
                                                {seller.Picture ? (
                                                    <img
                                                        className='wishlist-img'
                                                        src={seller.Picture}
                                                        alt="Slide"
                                                    />
                                                ) : (
                                                    <div className='d-block w-100 placeholder-image'>
                                                        No Image Available
                                                    </div>
                                                )}
                                            </Carousel.Item>
                                        )
                                    })}
                                </Carousel>

                            </div>

                            <div className="whishlist-hotel-card_info p-4">
                                <div className="align-items-center mb-2">
                                    <h5>{sellers[0].hotelName}</h5>
                                    <div className='d-flex hotel-stars'>
                                        {/* loop throw category and display number of stars */}
                                        {[...Array(parseInt(sellers[0].category))].map((index) => {
                                            return (
                                                <div key={index}>
                                                    <i className="fa fa-star text-warning"></i>
                                                </div>
                                            )
                                        })}
                                    </div>
                                    <div>
                                        <div className='remove-icon'>

                                            <svg height="25"
                                                 width="25" viewBox="0 0 128 128" role="presentation"
                                                 onClick={() => props.removeHotel(hotel['hotelId'])}
                                                 aria-hidden="true" focusable="false">
                                                <path d="M64 8a56 56 0 1 0 56 56A56 56 0 0 0 64 8zm22.2 69.8a6 6 0 1 1-8.4 8.4L64 72.5 50.2 86.2a6 6 0 0 1-8.4-8.4L55.5 64 41.8 50.2a6 6 0 0 1 8.4-8.4L64 55.5l13.8-13.7a6 6 0 0 1 8.4 8.4L72.5 64z"></path></svg>
                                        </div>
                                        {/* x icon so the user can remove the hotel */}

                                    </div>
                                </div>
                                <div className="d-flex justify-content-between align-items-end">
                                    <div className="whishlist-hotel-card_details">
                                        <div className="text-muted mb-2"><i className="fas fa-map-marker-alt"></i> {sellers[0].location}</div>
                                        {/* <div className="mb-2"><span className="badge badge-light">4.5</span> <a href="#!" className="text-muted">(245 ratings & 56 reviews)</a></div> */}
                                        <div className="amnities d-flex mb-3">
                                            <div className='whishlist-popularity'> {hotel['popularity']}</div>
                                        </div>
                                        {visibleSellers.slice(1).length > 0 &&
                                            <>
                                                <hr className='whishlist-sellers-line' />
                                                <div className="whishlist-other-seller d-flex flex-row pl-0 mb-0">
                                                    {/* the first seller is the cheapest it will be displayed alone */}
                                                    {visibleSellers.slice(1).map((seller) => {
                                                        return (
                                                            <div className="whishlist-other-seller d-flex align-items-center mr-3" key={`${seller.sellerId}` - `${seller.hotelId}`}>
                                                                {/* <img className="rounded-circle mr-2" src="https://picsum.photos/50/50/?image=11" alt="Seller"></img> */}
                                                                <div className="whishlist-seller-link d-flex flex-column">
                                                                    <a href={seller.detailsLink} className='whishlist-visible_sellers'
                                                                       onMouseDown={() => handleLinkClick(seller.sellerId)} target="_blank" rel="noopener noreferrer">
                                                                        <div className="whishlist-visible_sellers-name">{sellersInfo[seller.sellerId].name}</div>
                                                                        <small className="whishlist-visible_sellers-price">&nbsp;{seller.lowPrice.toFixed(3)}&nbsp;{seller.currency}</small>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        )
                                                    })}
                                                    {hiddenSellers.length > 0 && (
                                                        <div className='whishlist-dropdown '>
                                                            <Dropdown className='whishlist-hiddenSellers'>
                                                                <Dropdown.Toggle id="hiddenSellers-toggle">
                                                                    ({sellers.length - 3}) more
                                                                </Dropdown.Toggle>

                                                                <Dropdown.Menu className='whishlist-dropdown-menu-right'>
                                                                    {hiddenSellers.map(seller => {
                                                                        return (
                                                                            <Dropdown.Item id='hiddenSellers-item' href={seller.detailsLink}
                                                                                           key={`${seller.sellerId}` - `${seller.hotelId}`}
                                                                                           onMouseDown={() => handleLinkClick(seller.sellerId)}
                                                                                           target="_blank" rel="noopener noreferrer">
                                                                                <span className='whishlist-hiddenSeller-price'>{seller.lowPrice.toFixed(3)}&nbsp;{seller.currency}
                                                                                </span>&nbsp;
                                                                                <span className='whishlist-hiddenSeller-name'>{sellersInfo[seller.sellerId].name}
                                                                                </span>
                                                                            </Dropdown.Item>
                                                                        )
                                                                    })}
                                                                </Dropdown.Menu>
                                                            </Dropdown>
                                                        </div>
                                                    )}



                                                </div>
                                            </>
                                        }

                                    </div>
                                </div>
                            </div>

                            <div className='price'>

                                <div className='price_value'>
                                    {/* add discount div */}
                                    <span className='price_number'>{sellers[0].lowPrice.toFixed(3)}</span>&nbsp;
                                    <span className='price_currency'>{sellers[0].currency}</span>
                                    {/* <div className='price__discount'>20% off</div> */}
                                    <div className='price_name'>{sellersInfo[sellers[0].sellerId].name}</div>
                                </div>

                                <div className='button'>
                                    <a href={sellers[0].detailsLink} target="_blank" rel="noopener noreferrer">
                                        <button className='button_text' onMouseDown={() => handleLinkClick(sellers[0].sellerId)}>See details</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section >
        </div>
    )
}