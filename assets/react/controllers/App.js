import React, {useEffect, useState} from "react"
import NavBar from './NavBar';
import HotelCard from "./HotelCard";
import FilterByStar from "./FilterByStar";
import FilterBySeller from "./FilterBySeller";
import FilterByPrice from "./FilterByPrice";
import Dropdown from 'react-bootstrap/Dropdown';
import NoResultsCard from "./NoResultsCard";

export default function App() {
    const offset = 10;//number of hotels to load at a time
    const [availability, setAvailability] = useState(null);
    const [sellers, setSellers] = useState(null);
    const [hotels, setHotels] = useState(null);//hotels that get displayed in the page
    const [partialHotels, setPartialHotels] = useState(null);//hotels filters applied
    const [numHotels, setNumHotels] = useState(offset);
    const [isAtBottom, setIsAtBottom] = useState(false);
    const [stat, setStat] = useState('idle');//idle, success, no results, error
    const [sort, setSort] = useState('byPrice');//price,
    const [maxPrice, setMaxPrice] = useState(0);
    const [minPrice, setMinPrice] = useState(0);
    let count = 1;

    let [resultsMessage, setResultsMessage] = useState('');
    const [filters, setFilters] = useState([
        [],//sellers
        [0, 1, 2, 3, 4, 5],//stars
        [0, 0],//price

    ])
    //handle data received from symfony(hotelForm component)
    const handleDataReceived = (data) => {
        //reset filter
        setPartialHotels(null);
        setNumHotels(offset);
        setResultsMessage('');
        setFilters([
            [],//sellers
            [0, 1, 2, 3, 4, 5],//stars
            [0, 0],//price
        ])
        setAvailability(null);
        if (data === null || data.availability.length === 0) {
            setStat('no results');
            return null;
        }

        setStat('success');
        setNumHotels(offset);

        let av = sortSellers(data.availability);
        setAvailability(av);//availibility should not be changed
        setSellers(data.sellerInfo);

        //create an array the keys are sellerId and all values are false
        const initialCheckedState = {};
        Object.keys(data.sellerInfo).forEach(key => {
            initialCheckedState[key] = false;
        });
        setFilters([initialCheckedState, [0, 1, 2, 3, 4, 5], [0, Infinity]]);
    };

    //sort sellers in each hotel by price
    const sortSellers = (av) => {
        let min = Infinity;
        let max = 0;
        //get max and min price of all hotels
        const re = av.map(hotel => {
            hotel.sellers.forEach(seller => {
                if (seller.lowPrice > max) {
                    max = parseInt(seller.lowPrice);
                }
                if (seller.lowPrice < min) {
                    min = parseInt(seller.lowPrice);
                }
            });

            setMaxPrice(max);
            setMinPrice(min);

            let obj = {};
            obj.hotelId = hotel.hotelId;
            obj.sellers = hotel.sellers.sort((a, b) => a.lowPrice - b.lowPrice);
            return obj;
        })
        console.log('min', parseInt(min), 'max: ', parseInt(max))
        return re;
    }

    //set partial hotels when availibility changes(user clikc on search again)
    useEffect(() => {
        if (availability) {
            setPartialHotels(availability);
            setHotels(availability.slice(0, offset));
        }

    }, [availability]);

    //handle filters change
    useEffect(() => {
        if (partialHotels) {
            //filter hotels by seller
            const filteredHotels = availability.map(hotel => {
                return {
                    ...hotel,
                    sellers: hotel.sellers.filter(seller => {
                        //return true if sellerId is in filters and the value is true
                        return Object.entries(filters[0]).some(checkedSeller => {
                            return parseInt(checkedSeller[0]) === seller.sellerId && checkedSeller[1];
                        });
                    })
                }
            });

            // filter hotel by stars
            const filteredHotels2 = filteredHotels.map(hotel => {
                return {
                    ...hotel,
                    sellers: hotel.sellers.filter(seller => {
                        return filters[1].includes(parseInt(seller.category));
                    })
                }
            });
            console.log('filteredHotels2', filteredHotels2)

            // filter hotel by price
            const filteredHotels3 = filteredHotels2.map(hotel => {
                return {
                    ...hotel,
                    sellers: hotel.sellers.filter(seller => {
                        return parseFloat(seller.lowPrice) >= parseFloat(filters[2][0]) && parseFloat(seller.lowPrice) <= parseFloat(filters[2][1]);
                    })
                }
            });
            // console.log('filteredHotels3', filteredHotels3)
            // sort the remaining hotels by price
            let filteredHotels4 = null;
            if (sort === 'byPrice') {
                filteredHotels4 = sortByPrice(filteredHotels3)
                setPartialHotels(filteredHotels4);
            } else {
                setPartialHotels(filteredHotels3);
            }
            // setAvailability(filteredHotels2)

        }
    }, [filters, sort])


    // every time filter changes re-render hotels(page hotels)
    useEffect(() => {
        partialHotels && setHotels(partialHotels.slice(0, numHotels));
    }, [partialHotels, numHotels])


    // keep track of scroll position
    useEffect(() => {
        const handleScroll = () => {
            const scrollTop = document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight;
            const clientHeight = document.documentElement.clientHeight;
            //if the user is 50 pixels away from the bottom of the page trigger the event
            if (scrollTop + clientHeight >= scrollHeight - 50) {
                setIsAtBottom(true);
            } else {
                setIsAtBottom(false);
            }
        };
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    //load more data when user scrolls to bottom
    useEffect(() => {
        if (partialHotels && numHotels < partialHotels.length) {
            setResultsMessage('Loading more results...');
            if (isAtBottom)
                setNumHotels(numHotels + offset);
            // if(numHotels+offset>partialHotels.length)
            //     setResultsMessage('End of results');
        }
        if (partialHotels && numHotels >= partialHotels.length) {
            setResultsMessage('End of results');
        }

    }, [isAtBottom, hotels]);


    const sortByPrice = hotels => {
        let hotelsCopy = [...hotels].sort((hotel1, hotel2) => {
            if (hotel1.sellers.length === 0 && hotel2.sellers.length === 0) {
                return 0; // No sellers for both hotels, consider them equal
            } else if (hotel1.sellers.length === 0) {
                return 1; // hotel1 has no sellers, move it to the end
            } else if (hotel2.sellers.length === 0) {
                return -1; // hotel2 has no sellers, move it to the end
            } else {
                return hotel1.sellers[0].lowPrice - hotel2.sellers[0].lowPrice;
            }
        });

        return hotelsCopy;
    }

    return (
        <>
            <NavBar onDataReceived={handleDataReceived} />
            <section id="explore_area" className="section_padding">
                <div className="container">
                    {availability &&
                        <div className="row">
                            <div className="col-lg-12 col-md-12 col-sm-12 col-12">
                                <div className="section_heading_center">
                                    <h2>{availability.length} hotel found</h2>
                                </div>
                            </div>
                        </div>
                    }
                    <div className="row">
                        <div className="col-lg-3 filters-row">

                            {hotels &&
                                <>

                                    <div className="left_side_search_area">
                                        <FilterByPrice
                                            setFilters={setFilters}
                                            minPrice={minPrice}
                                            maxPrice={maxPrice}
                                        />
                                    </div>
                                    <div className="left_side_search_area">
                                        <FilterByStar
                                            availability={availability}
                                            setFilters={setFilters}
                                        />
                                    </div>
                                    <div className="left_side_search_area">
                                        <FilterBySeller
                                            sellers={sellers}
                                            setFilters={setFilters}
                                        />
                                    </div>
                                    <div className="left_side_search_area">

                                    </div>
                                </>
                            }
                        </div>

                        <div className="col-lg-9">
                            <div className="row">
                                {hotels &&
                                    <div className="container">
                                        <div className="sort-by">Sort by</div>
                                        <div className="dropdown-sort">
                                            <button></button>
                                            <div className="options">
                                                <input id="radio-price" type="radio" name="region" value="price" defaultChecked onClick={() => setSort('byPrice')} />
                                                <label style={{ "--index": 1 }} htmlFor="radio-price">price(lowest first)</label>
                                                <input id="radio-other" type="radio" name="region" value="other" onClick={() => setSort('other')} />
                                                <label style={{ "--index": 2 }} htmlFor="radio-other">other</label>
                                            </div>
                                            <svg viewBox="0 0 24 24">
                                                <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z" />
                                            </svg>
                                        </div>
                                    </div>}

                                {partialHotels &&
                                    hotels.map(hotel => {
                                        {
                                            if (hotel.sellers.length >= 1) {
                                                return (
                                                    <div key={hotel.hotelId}>
                                                        <HotelCard key={`hotel-card-${hotel.hotelId}`} sellersInfo={sellers}
                                                                   sellers={hotel.sellers} />
                                                    </div>
                                                );
                                            }
                                        }

                                    })

                                }
                                {stat === 'no results' &&  <NoResultsCard />}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    )
}