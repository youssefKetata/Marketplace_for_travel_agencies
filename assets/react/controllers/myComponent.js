// import React, { useState } from 'react';
//
// function HotelSearch() {
//     const [location, setLocation] = useState('');
//     const [checkIn, setCheckIn] = useState(new Date());
//     const [checkOut, setCheckOut] = useState(() => new Date(Date.now() + 24 * 60 * 60 * 1000));
//     const todayDate = new Date().toISOString().split("T")[0];
//     const [rooms, setRooms] = useState([{ id: 1, adults: 1, children: [] }]);
//     const [errorLocation, setErrorLocation] = useState('');
//     const [errorRoom, setErrorRoom] = useState('');
//     const [errorDate, setErrorDate] = useState('');
//
//     const handleAddRoom = () => {
//         event.stopPropagation();//prevents the dropdown menu from closing when the buttons inside it are clicked.
//         const newRoomId = rooms.length + 1;
//         setRooms([...rooms, { id: newRoomId, adults: 0, children: [] }]);
//     };
//
//     const handleRemoveRoom = (roomId) => {
//         event.stopPropagation();
//         if (rooms.length === 1) {
//             return; // Do not remove the only room
//         }
//         setRooms((prevRooms) => prevRooms.filter((room) => room.id !== roomId));
//         //reset the ids of the remaining rooms
//         setRooms((prevRooms) => prevRooms.map((room, index) => {
//             return { ...room, id: index + 1 };
//         }));
//     };
//
//     // Function to handle increment of adults count
//     const handleAdultCountIncrease = (roomId) => {
//         event.stopPropagation();
//         setRooms(prevRooms => {
//             return prevRooms.map(room => {
//                 if (room.id === roomId) {
//                     return { ...room, adults: room.adults + 1 };
//                 }
//                 return room;
//             });
//         });
//     };
//
//     // Function to handle decrement of adults count
//     const handleAdultCountDecrease = (roomId) => {
//         event.stopPropagation();
//         setRooms(prevRooms => {
//             return prevRooms.map(room => {
//                 if (room.id === roomId && room.adults > 0) {
//                     return { ...room, adults: room.adults - 1 };
//                 }
//                 return room;
//             });
//         });
//     };
//
//
//     const handleChildAgeChange = (roomId, childIndex, age) => {
//         event.stopPropagation();
//         setRooms(
//             rooms.map((room) => {
//                 if (room.id === roomId) {
//                     const children = [...room.children];
//                     children[childIndex] = Number(age);
//                     return { ...room, children };
//                 }
//                 return room;
//             })
//         );
//     };
//
//     const handleAddChild = (roomId) => {
//         event.stopPropagation();
//         setRooms(
//             rooms.map((room) => {
//                 if (room.id === roomId) {
//                     const children = [...room.children, 0];
//                     return { ...room, children };
//                 }
//                 return room;
//             })
//         );
//     };
//
//     const handleRemoveChild = (roomId, childIndex) => {
//         event.stopPropagation();
//         setRooms(
//             rooms.map((room) => {
//                 if (room.id === roomId) {
//                     const children = [...room.children];
//                     children.splice(childIndex, 1);
//                     return { ...room, children };
//                 }
//                 return room;
//             })
//         );
//     };
//
//     const personsCount = rooms.reduce((total, room) => {
//         return total + room.adults + room.children.length;
//     }, 0);
//
//     const handleLocationChange = e => {
//         setErrorLocation(''); // reset the error message
//         setLocation(e.target.value)
//     }
//
//     const handleCheckInChange = e => {
//         setCheckIn(new Date(e.target.value))
//         if (checkIn === checkOut) {
//             setCheckOut(new Date(e.target.value + 24 * 60 * 60 * 1000))
//         }
//     }
//     const handleCheckOutChange = e => {
//         setCheckOut(new Date(e.target.value))
//
//     }
//
//     const getWeekday = (date) => {
//         const options = { weekday: "long" };
//         date = new Date(date);
//         return date.toLocaleDateString("en-US", options);
//     };
//
//
//
//     const handleSubmit = async e => {
//         e.preventDefault();
//         if (location === '') {
//             //add error message in form input location
//             setErrorLocation('Location is required');
//             return; // return early to prevent sending data to the server
//         }
//         if (rooms.length === 0) {
//             setErrorRoom('Rooms is required');
//             return;
//         }
//         if (checkIn === checkOut || checkIn > checkOut) {
//             setErrorDate('Check-in date must be before check-out date');
//             return;
//         }
//
//         const data = {
//             productType: productType,
//             location: location,
//             checkIn: checkIn,
//             checkOut: checkOut,
//             rooms: rooms,
//         }
//         console.log(JSON.stringify(data));
//
//         try {
//             const response = await fetch(
//                 'http://user1-market.3t.tn/search/hotel/api/getFormData', {
//                     method: 'POST',
//                     headers: {
//                         'Content-Type': 'application/json',
//                     },
//                     body: JSON.stringify(data),
//                 }
//             );
//             if (!response.ok) {
//                 throw new Error('Network response was not ok');
//             }
//             const responseData = await response.text();
//             console.log(responseData);
//
//         } catch (error) {
//             console.log(error);
//             // Handle error as needed
//         }
//     }
//
//
//
//     // prevent the user form choosing a check-out date before or the same day as checkIn date
//     if (checkOut < checkIn) {
//         checkOut.setDate(checkIn.getDate() + 1);
//     }
//     // prevent the user from choosing a date earlier than today
//     if (checkIn < new Date()) {
//         setCheckIn(new Date());
//     }
//
//     const nbNights =
//         Math.floor((checkOut - checkIn) / (1000 * 3600 * 24))>0 ? Math.floor((checkOut - checkIn) / (1000 * 3600 * 24)) : 1;
//
//     //const nbNights = Math.floor((checkOut - checkIn) / (1000 * 3600 * 24));
//
//     return (
//         <>
//             <section id="theme_search_form">
//                 <div className="container">
//                     <div className="row">
//                         <div className="col-lg-12">
//                             <div className="theme_search_form_area">
//                                 <div className="theme_search_form_tabbtn">
//                                     <ul className="nav nav-tabs" role="tablist">
//                                         <li className="nav-item" role="presentation">
//                                             <button className="nav-link active" id="hotels-tab" data-bs-toggle="tab"
//                                                     data-bs-target="#hotels" type="button" role="tab" aria-controls="hotels"
//                                                     aria-selected="true"><i className="fas fa-hotel"></i>Hotels</button>
//                                         </li>
//                                         <li className="nav-item" role="presentation">
//                                             <button className="nav-link" id="flights-tab" data-bs-toggle="tab"
//                                                     data-bs-target="#flights" type="button" role="tab" aria-controls="flights"
//                                                     aria-selected="false"><i className="fas fa-plane-departure"></i>Flights</button>
//                                         </li>
//                                         <li className="nav-item" role="presentation">
//                                             <button className="nav-link" id="tours-tab" data-bs-toggle="tab" data-bs-target="#tours"
//                                                     type="button" role="tab" aria-controls="tours" aria-selected="false"><i
//                                                 className="fas fa-globe"></i>Tours</button>
//                                         </li>
//                                         <li className="nav-item" role="presentation">
//                                             <button className="nav-link" id="visa-tab" data-bs-toggle="tab"
//                                                     data-bs-target="#visa-application" type="button" role="tab" aria-controls="visa"
//                                                     aria-selected="false"><i className="fas fa-passport"></i> Visa</button>
//                                         </li>
//                                     </ul>
//                                 </div>
//                                 <div className="tab-content" id="myTabContent">
//                                     <div className="tab-pane fade show active" id="hotels" role="tabpanel" aria-labelledby="hotels-tab">
//                                         <div className="row">
//                                             <div className="col-lg-12">
//                                                 <div className="tour_search_form">
//                                                     <form onSubmit={handleSubmit}>
//                                                         <div className="row">
//                                                             <div className="col-lg-6 col-md-12 col-sm-12 col-12">
//                                                                 <div className="flight_Search_boxed">
//                                                                     <p>Destination</p>
//                                                                     <input type="text" value={location} onChange={handleLocationChange} placeholder="Where are you going?"></input>
//                                                                     <p className="font-italic text-danger">{errorLocation && errorLocation}</p>
//
//                                                                     <span>Where are you going?</span>
//                                                                 </div>
//                                                             </div>
//                                                             <div className="col-lg-4 col-md-6 col-sm-12 col-12">
//                                                                 <div className="form_search_date">
//                                                                     <div className="flight_Search_boxed date_flex_area">
//                                                                         <div className="Journey_date">
//                                                                             <p>Journey date</p>
//                                                                             <input type="date"
//                                                                                    value={checkIn.toISOString().substr(0, 10)}
//                                                                                    min={todayDate}
//                                                                                    onChange={handleCheckInChange}>
//                                                                             </input>
//                                                                             <span>{getWeekday(checkIn)}</span>
//                                                                         </div>
//                                                                         <div className="Journey_date">
//                                                                             <p>Return date</p>
//                                                                             <input type="date"
//                                                                                    value={checkOut.toISOString().substr(0, 10)}
//                                                                                    min={new Date(checkIn.getTime() + 24 * 60 * 60 * 1000).toISOString().substr(0, 10)}
//                                                                                    onChange={handleCheckOutChange}>
//                                                                             </input>
//                                                                             <span>{getWeekday(checkOut)} ({nbNights} nights)</span>
//                                                                         </div>
//                                                                     </div>
//                                                                 </div>
//                                                                 <p className="font-italic text-danger">{errorDate && errorDate}</p>
//                                                             </div>
//
//                                                             {/* start passenger area */}
//                                                             <div className="col-lg-2  col-md-6 col-sm-12 col-12">
//                                                                 <div className="flight_Search_boxed dropdown_passenger_area">
//                                                                     <p>Passenger</p>
//                                                                     <div className="dropdown">
//                                                                         <button className="dropdown-toggle final-count"
//                                                                                 data-toggle="dropdown" type="button"
//                                                                                 id="dropdownMenuButton1" data-bs-toggle="dropdown"
//                                                                                 aria-expanded="false">
//                                                                             {personsCount} Passengers
//                                                                         </button>
//                                                                         <div className="dropdown-menu dropdown_passenger_info"
//                                                                              aria-labelledby="dropdownMenuButton1">
//                                                                             <div className="traveller-calulate-persons">
//                                                                                 <div className="passengers">
//                                                                                     <h6>Rooms</h6>
//                                                                                     <div className="passengers-types">
//                                                                                         <div>
//                                                                                             {rooms.map((room, index) => (
//                                                                                                 <div key={room.id}>
//                                                                                                     <span>Room {index + 1}</span>
//                                                                                                     <div className='passengers-type'>
//                                                                                                         <div className="text">
//                                                                                                             {room.adults} &nbsp;Adults
//                                                                                                         </div>
//                                                                                                         <div className="button-set">
//                                                                                                             <button type="button"
//                                                                                                                     className="btn-add"
//                                                                                                                     onClick={() => handleAdultCountIncrease(room.id)}>
//                                                                                                                 <i className="fas fa-plus"></i>
//                                                                                                             </button>
//                                                                                                             <button type="button"
//                                                                                                                     className="btn-subtract"
//                                                                                                                     onClick={() => handleAdultCountDecrease(room.id)}
//                                                                                                             >
//                                                                                                                 <i className="fas fa-minus"></i>
//                                                                                                             </button>
//                                                                                                         </div>
//                                                                                                     </div>
//                                                                                                     <div className='passengers-type'>
//                                                                                                         <div className='text'> {room.children.length} &nbsp; Children</div>
//                                                                                                             <div className="button-set">
//                                                                                                                 <button type="button"
//                                                                                                                         className="btn-add"
//                                                                                                                         onClick={() => handleAddChild(room.id)}>
//                                                                                                                     <i className="fas fa-plus"></i>
//                                                                                                                 </button>
//                                                                                                         </div>
//                                                                                                     </div>
//                                                                                                     <div className='passengers-type'>
//                                                                                                         <div id='children'>
//                                                                                                             {room.children.map((age, childIndex) => (
//                                                                                                                 <div key={childIndex} className="child-block">
//                                                                                                                     <div className="child-text">child 1
//                                                                                                                         <div className="form-group col-sm-10">
//                                                                                                                             <input type="number" onChange={(e) => handleChildAgeChange(room.id, childIndex, e.target.value)} className="form-control" id="age"></input>
//                                                                                                                         </div>
//                                                                                                                     </div>
//                                                                                                                     <div className="button-set">
//                                                                                                                         <button type="button"
//                                                                                                                                 className="btn-subtract-in"
//                                                                                                                                 onClick={() => handleRemoveChild(room.id, childIndex)}>
//                                                                                                                             <i className="fas fa-minus"></i>
//                                                                                                                         </button>
//                                                                                                                     </div>
//                                                                                                                 </div>
//                                                                                                             ))}
//                                                                                                         </div>
//
//                                                                                                     </div>
//                                                                                                     {rooms.length > 1 && (
//                                                                                                         <button type='button' onClick={() => handleRemoveRoom(room.id)}>
//                                                                                                             Remove Room
//                                                                                                         </button>
//                                                                                                     )}
//                                                                                                 </div>
//                                                                                             ))}
//                                                                                             <button type='button' onClick={() => handleAddRoom()}>Add Room</button>
//                                                                                         </div>
//                                                                                     </div>
//                                                                                 </div>
//                                                                             </div>
//                                                                         </div>
//                                                                     </div>
//                                                                     <span>{rooms.length} Rooms</span>
//                                                                 </div>
//                                                                 <p className="font-italic text-danger">{errorRoom && errorRoom}</p>
//                                                             </div>
//                                                             {/* end passenger area */}
//
//                                                             <div className="top_form_search_button">
//                                                                 <button type='submit' className="btn btn_theme btn_md">Search</button>
//                                                             </div>
//                                                         </div>
//                                                     </form>
//                                                 </div>
//                                             </div>
//                                         </div>
//                                     </div>
//                                     <div className="tab-pane fade" id="tours" role="tabpanel" aria-labelledby="tours-tab">
//                                         <div className="row">
//                                             <div className="col-lg-12">
//                                                 <div className="tour_search_form">
//                                                     <form action="#!">
//                                                         <div className="row">
//                                                             <div className="col-lg-6 col-md-12 col-sm-12 col-12">
//                                                                 <div className="flight_Search_boxed">
//                                                                     <p>Destination</p>
//                                                                     <input type="text"
//                                                                            placeholder="Where are you going?">
//                                                                         <span>Where are you going?</span></input>
//                                                                 </div>
//                                                             </div>
//                                                             <div className="col-lg-4 col-md-6 col-sm-12 col-12">
//                                                                 <div className="form_search_date">
//                                                                     <div className="flight_Search_boxed date_flex_area">
//                                                                         <div className="Journey_date">
//                                                                             <p>Journey date</p>
//                                                                             <input type="date" value="2022-05-03">
//                                                                                 <span>Thursday</span></input>
//                                                                         </div>
//                                                                         <div className="Journey_date">
//                                                                             <p>Return date</p>
//                                                                             <input type="date" value="2022-05-05">
//                                                                                 <span>Thursday</span></input>
//                                                                         </div>
//                                                                     </div>
//                                                                 </div>
//                                                             </div>
//                                                             <div className="col-lg-2  col-md-6 col-sm-12 col-12">
//                                                                 <div
//                                                                     className="flight_Search_boxed dropdown_passenger_area">
//                                                                     <p>Passenger, Class </p>
//                                                                     <div className="dropdown">
//                                                                         <button className="dropdown-toggle final-count"
//                                                                                 data-toggle="dropdown" type="button"
//                                                                                 id="dropdownMenuButton1"
//                                                                                 data-bs-toggle="dropdown"
//                                                                                 aria-expanded="false">
//                                                                             0 Passenger
//                                                                         </button>
//                                                                         <div
//                                                                             className="dropdown-menu dropdown_passenger_info"
//                                                                             aria-labelledby="dropdownMenuButton1">
//                                                                             <div className="traveller-calulate-persons">
//                                                                                 <div className="passengers">
//                                                                                     <h6>Passengers</h6>
//                                                                                     <div className="passengers-types">
//                                                                                         <div
//                                                                                             className="passengers-type">
//                                                                                             <div className="text"><span
//                                                                                                 className="count pcount">2</span>
//                                                                                                 <div
//                                                                                                     className="type-label">
//                                                                                                     <p>Adult</p>
//                                                                                                     <span>12+
//                                                                                                 yrs</span>
//                                                                                                 </div>
//                                                                                             </div>
//                                                                                             <div className="button-set">
//                                                                                                 <button type="button"
//                                                                                                         className="btn-add">
//                                                                                                     <i className="fas fa-plus"></i>
//                                                                                                 </button>
//                                                                                                 <button type="button"
//                                                                                                         className="btn-subtract">
//                                                                                                     <i className="fas fa-minus"></i>
//                                                                                                 </button>
//                                                                                             </div>
//                                                                                         </div>
//                                                                                         <div
//                                                                                             className="passengers-type">
//                                                                                             <div className="text"><span
//                                                                                                 className="count ccount">0</span>
//                                                                                                 <div
//                                                                                                     className="type-label">
//                                                                                                     <p className="fz14 mb-xs-0">
//                                                                                                         Children
//                                                                                                     </p><span>2
//                                                                                                 - Less than 12
//                                                                                                 yrs</span>
//                                                                                                 </div>
//                                                                                             </div>
//                                                                                             <div className="button-set">
//                                                                                                 <button type="button"
//                                                                                                         className="btn-add-c">
//                                                                                                     <i className="fas fa-plus"></i>
//                                                                                                 </button>
//                                                                                                 <button type="button"
//                                                                                                         className="btn-subtract-c">
//                                                                                                     <i className="fas fa-minus"></i>
//                                                                                                 </button>
//                                                                                             </div>
//                                                                                         </div>
//                                                                                         <div
//                                                                                             className="passengers-type">
//                                                                                             <div className="text"><span
//                                                                                                 className="count incount">0</span>
//                                                                                                 <div
//                                                                                                     className="type-label">
//                                                                                                     <p className="fz14 mb-xs-0">
//                                                                                                         Infant
//                                                                                                     </p><span>Less
//                                                                                                 than 2
//                                                                                                 yrs</span>
//                                                                                                 </div>
//                                                                                             </div>
//                                                                                             <div className="button-set">
//                                                                                                 <button type="button"
//                                                                                                         className="btn-add-in">
//                                                                                                     <i className="fas fa-plus"></i>
//                                                                                                 </button>
//                                                                                                 <button type="button"
//                                                                                                         className="btn-subtract-in">
//                                                                                                     <i className="fas fa-minus"></i>
//                                                                                                 </button>
//                                                                                             </div>
//                                                                                         </div>
//                                                                                     </div>
//                                                                                 </div>
//                                                                                 <div className="cabin-selection">
//                                                                                     <h6>Cabin Class</h6>
//                                                                                     <div className="cabin-list">
//                                                                                         <button type="button"
//                                                                                                 className="label-select-btn">
//                                                                                     <span
//                                                                                         className="muiButton-label">Economy
//                                                                                     </span>
//                                                                                         </button>
//                                                                                         <button type="button"
//                                                                                                 className="label-select-btn active">
//                                                                                     <span className="muiButton-label">
//                                                                                         Business
//                                                                                     </span>
//                                                                                         </button>
//                                                                                         <button type="button"
//                                                                                                 className="label-select-btn">
//                                                                                     <span className="MuiButton-label">First
//                                                                                         Class </span>
//                                                                                         </button>
//                                                                                     </div>
//                                                                                 </div>
//                                                                             </div>
//                                                                         </div>
//                                                                     </div>
//                                                                     <span>Business</span>
//                                                                 </div>
//                                                             </div>
//                                                             <div className="top_form_search_button">
//                                                                 <button className="btn btn_theme btn_md">Search</button>
//                                                             </div>
//                                                         </div>
//                                                     </form>
//                                                 </div>
//                                             </div>
//                                         </div>
//                                     </div>
//
//
//                                 </div>
//                             </div>
//                         </div>
//                     </div>
//                 </div>
//             </section>
//
//         </>
//     )
// }
// export default HotelSearch;
