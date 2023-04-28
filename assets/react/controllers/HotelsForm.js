import React, { useState, useRef } from "react";
import axios from "axios";
import { redirect } from "react-router-dom";
//import { useNavigate } from "react-router-dom";

const HotelsForm = () => {
    const [location, setLocation] = useState('');
    const [checkIn, setCheckIn] = useState(new Date());
    const [checkOut, setCheckOut] = useState(() => new Date(Date.now() + 24 * 60 * 60 * 1000));
    const todayDate = new Date().toISOString().split("T")[0];
    const [rooms, setRooms] = useState([{ id: 1, adults: 1, children: [] }]);
    const [occupancies, setOccupancies] = useState({ 1: { adult: 1, child: { value: 0, age: 0 } } });
    const [errorLocation, setErrorLocation] = useState('');
    const [errorRoom, setErrorRoom] = useState('');
    const [errorDate, setErrorDate] = useState('');
    const [status, setStatus] = useState('idle');
    const productType = "hotels";
    const checkOutRef = useRef(null);
    //const navigate = useNavigate();
    const handleAddRoom = () => {
        event.stopPropagation();
        const newRoomId = rooms.length + 1;
        setRooms([...rooms, { id: newRoomId, adults: 0, children: [] }]);
    };

    const handleRemoveRoom = (roomId) => {
        event.stopPropagation();
        if (rooms.length === 1) {
            return; // Do not remove the only room
        }
        setRooms((prevRooms) => prevRooms.filter((room) => room.id !== roomId));
        //reset the ids of the remaining rooms
        setRooms((prevRooms) => prevRooms.map((room, index) => {
            return { ...room, id: index + 1 };
        }));
    };

    // Function to handle increment of adults count
    const handleAdultCountIncrease = (roomId) => {
        event.stopPropagation();
        setRooms(prevRooms => {
            return prevRooms.map(room => {
                if (room.id === roomId) {
                    return { ...room, adults: room.adults + 1 };
                }
                return room;
            });
        });
    };

    // Function to handle decrement of adults count
    const handleAdultCountDecrease = (roomId) => {
        event.stopPropagation();
        setRooms(prevRooms => {
            return prevRooms.map(room => {
                if (room.id === roomId && room.adults > 0) {
                    return { ...room, adults: room.adults - 1 };
                }
                return room;
            });
        });
    };


    const handleChildAgeChange = (roomId, childIndex, age) => {
        event.stopPropagation();
        setRooms(
            rooms.map((room) => {
                if (room.id === roomId) {
                    const children = [...room.children];
                    children[childIndex] = Number(age);
                    return { ...room, children };
                }
                return room;
            })
        );
    };

    const handleAddChild = (roomId) => {
        event.stopPropagation();
        setRooms(
            rooms.map((room) => {
                if (room.id === roomId) {
                    const children = [...room.children, 0];
                    return { ...room, children };
                }
                return room;
            })
        );
    };
    const handleRemoveChild = (roomId) => {
        event.stopPropagation();
        setRooms(prevRooms => {
            return prevRooms.map(room => {
                if (room.id === roomId && room.children.length > 0) {
                    const children = [...room.children];
                    children.pop();
                    return { ...room, children };
                }
                return room;
            });
        });
    };


    // const handleRemoveChild = (roomId, childIndex) => {
    //     event.stopPropagation();
    //     setRooms(
    //         rooms.map((room) => {
    //             if (room.id === roomId) {
    //                 const children = [...room.children];
    //                 children.splice(childIndex, 1);
    //                 return { ...room, children };
    //             }
    //             return room;
    //         })
    //     );
    // };

    const personsCount = rooms.reduce((total, room) => {
        return total + room.adults + room.children.length;
    }, 0);

    const handleLocationChange = e => {
        setErrorLocation(''); // reset the error message
        setLocation(e.target.value)
    }

    const handleCheckInChange = e => {
        if(e.target.value){
            setCheckIn(new Date(e.target.value));
            checkOutRef.current.focus();
        }
        else setCheckIn(new Date())
        if (checkIn === checkOut || checkIn > checkOut) {
            const checkoutDate = new Date(e.target.value);
            checkoutDate.setDate(checkoutDate.getDate() + 1);
            setCheckOut(checkoutDate);
        }
    }
    const handleCheckOutChange = e => {
        if(e.target.value && (new Date(e.target.value)) > checkIn)
            setCheckOut(new Date(e.target.value))
        else setCheckOut(new Date())

    }

    const getWeekday = (date) => {
        const options = { weekday: "long" };
        date = new Date(date);
        return date.toLocaleDateString("en-US", options);
    };

    const validForm = () => {
        if (location === '') {
            setErrorLocation('Location is required');
            return false;
        }
        if (rooms.length === 0) {
            setErrorRoom('Rooms is required');
            return false;
        }
        if (checkIn === checkOut || checkIn > checkOut) {
            setErrorDate('Check-in date must be before check-out date');
            return false;
        }
        return true;
    }

    const dateForm = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Month is zero-based, so add 1 and pad with leading zero
        const day = String(date.getDate()).padStart(2, '0'); // Pad day with leading zero

        // Format date string in "YYYY-MM-DD" format
        return `${year}-${month}-${day}`;
    }


    function convertData(data) {
        const result = {};
        for (let i = 0; i < data.length; i++) {
            const { id, adults, children } = data[i];
            const childValue = children.length;
            const childAge = children.join(',');
            result[id] = {
                adult: adults.toString(),
                child: {
                    value: childValue,
                    age: childAge,
                },
            };
        }
        return result;
    }



    const handleSubmit = async e => {
        e.preventDefault();

        if (!validForm()) {
            return;
        }



        const data = {
            productType: productType,
            location: location,
            checkIn: dateForm(checkIn),
            checkOut: dateForm(checkOut),
            rooms: convertData(rooms),
        }

        let data2 = JSON.stringify(data);
        setStatus('loading');

        try {
            const response = await axios.post('http://user1-market.3t.tn/search/getFormData', data, {
                headers: {
                    'Content-Type': 'application/json',
                    'Access-Control-Allow-Origin': '*'
                },
            });
            if (response.status === 200) {
                console.log('response: ', response)
                const result = response.data;
                setStatus('done');
                //window.location.href = 'http://user1-market.3t.tn/search/hotel/api/getFormData';

            } else {
                setStatus('error');
                console.log(response.status);
            }
            //navigate('http://user1-market.3t.tn/search/hotel/api/getFormData');

        } catch (error) {
            console.log(error);
            setStatus('error');
        }
    }



    // prevent the user form choosing a check-out date before or the same day as checkIn date
    if (checkOut < checkIn) {
        checkOut.setDate(checkIn.getDate() + 1);
    }
    // prevent the user from choosing a date earlier than today
    if (checkIn < new Date()) {
        setCheckIn(new Date());
    }

    const nbNights = Math.floor((checkOut - checkIn) / (1000 * 3600 * 24)) > 0 ? Math.floor((checkOut - checkIn) / (1000 * 3600 * 24)) : 1;


    return (
        <div className="tab-pane fade show active" id="hotels" role="tabpanel" aria-labelledby="hotels-tab">
            <div className="row">
                <div className="col-lg-12">
                    <div className="tour_search_form">
                        <form onSubmit={handleSubmit}>
                            <div className="row">
                                <div className="col-lg-6 col-md-12 col-sm-12 col-12">
                                    <div className="flight_Search_boxed">
                                        <p>Destination</p>
                                        <input type="text" value={location} onChange={handleLocationChange} placeholder="Where are you going?"></input>
                                        <span>Where are you going?</span>
                                    </div>
                                    <p className="font-italic text-danger">{errorLocation && errorLocation}</p>
                                </div>
                                <div className="col-lg-4 col-md-6 col-sm-12 col-12">
                                    <div className="form_search_date">
                                        <div className="flight_Search_boxed date_flex_area">
                                            <div className="Journey_date">
                                                <p>Journey date</p>
                                                <input type="date"
                                                       value={checkIn.toISOString().substr(0, 10)}
                                                       min={todayDate}
                                                       onChange={handleCheckInChange}>
                                                </input>
                                                <span>{getWeekday(checkIn)}</span>
                                            </div>
                                            <div className="Journey_date">
                                                <p>Return date</p>
                                                <input type="date"
                                                       value={checkOut.toISOString().substr(0, 10)}
                                                       min={new Date(checkIn.getTime() + 24 * 60 * 60 * 1000).toISOString().substr(0, 10)}
                                                       onChange={handleCheckOutChange}
                                                       ref={checkOutRef}
                                                >
                                                </input>
                                                <span>{getWeekday(checkOut)} ({nbNights} nights)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <p className="font-italic text-danger">{errorDate && errorDate}</p>
                                </div>

                                {/* start passenger area */}
                                <div className="col-lg-2  col-md-6 col-sm-12 col-12">
                                    <div className="flight_Search_boxed dropdown_passenger_area">
                                        <p>Passenger</p>
                                        <div className="dropdown">
                                            <button className="dropdown-toggle final-count"
                                                    data-toggle="dropdown" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                {personsCount} Passengers
                                            </button>
                                            <div className="dropdown-menu dropdown_passenger_info"
                                                 aria-labelledby="dropdownMenuButton1">
                                                <div className="traveller-calulate-persons">
                                                    <div className="passengers">
                                                        <h6>Rooms</h6>
                                                        <button type='button' onClick={() => handleAddRoom()}><i className="fas fa-plus"></i>&nbsp;Add Room</button>
                                                        <div className="passengers-types">
                                                            <div>
                                                                {rooms.map((room, index) => (
                                                                    <div key={room.id}>
                                                                        <span>Room {index + 1}</span>
                                                                        <div className='passengers-type'>
                                                                            <div className="text">
                                                                                {room.adults} &nbsp;Adults
                                                                            </div>
                                                                            <div className="button-set">
                                                                                <button type="button"
                                                                                        className="btn-add"
                                                                                        onClick={() => handleAdultCountIncrease(room.id)}>
                                                                                    <i className="fas fa-plus"></i>
                                                                                </button>
                                                                                <button type="button"
                                                                                        className="btn-subtract"
                                                                                        onClick={() => handleAdultCountDecrease(room.id)}
                                                                                >
                                                                                    <i className="fas fa-minus"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                        <div className='passengers-type'>
                                                                            <div className='text'> {room.children.length} &nbsp; Children</div>
                                                                            <div className="button-set">
                                                                                <button type="button"
                                                                                        className="btn-add"
                                                                                        onClick={() => handleAddChild(room.id)}>
                                                                                    <i className="fas fa-plus"></i>
                                                                                </button>
                                                                                <button type="button"
                                                                                        className="btn-subtract"
                                                                                        onClick={() => handleRemoveChild(room.id)}
                                                                                >
                                                                                    <i className="fas fa-minus"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                        <div className='passengers-type'>
                                                                            <div id='children'>
                                                                                {room.children.map((age, childIndex) => (
                                                                                    <div key={childIndex} className="">
                                                                                        <div className="child-text">
                                                                                            <div className="form-group col-sm-10">
                                                                                                child {childIndex + 1} &nbsp;
                                                                                                <input
                                                                                                    type="number"
                                                                                                    onChange={(e) => handleChildAgeChange(room.id, childIndex, e.target.value)}
                                                                                                    className="form-control"
                                                                                                    id="age">
                                                                                                </input>
                                                                                            </div>

                                                                                        </div>
                                                                                    </div>
                                                                                ))}
                                                                            </div>

                                                                        </div>
                                                                        {rooms.length > 1 && (
                                                                            <button type='button' onClick={() => handleRemoveRoom(room.id)}>
                                                                                <i className="fas fa-minus"></i>&nbsp;Remove room
                                                                            </button>
                                                                        )}
                                                                    </div>
                                                                ))}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span>{rooms.length} Rooms</span>
                                    </div>
                                    <p className="font-italic text-danger">{errorRoom && errorRoom}</p>
                                </div>
                                {/* end passenger area */}

                                <div className="top_form_search_button">
                                    <button type='submit' className="btn btn_theme btn_md">Search</button>
                                </div>
                            </div>
                        </form>
                        {status === 'loading' && <div>loading</div>}
                        {status === 'error' && <div>error</div>}
                    </div>
                </div>
            </div>
        </div>

    )
}

export default HotelsForm;