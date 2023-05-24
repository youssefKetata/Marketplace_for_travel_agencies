import React from "react";
import ReactDOM from "react-dom/client";
import {StrictMode} from "react";
// import HotelCard2 from "./React/controllers/HotelCard2";

const myComponent = ReactDOM.createRoot(document.getElementById('HotelCard2'));
const element = document.getElementById("HotelCard2");
const data = element.getAttribute("data-hotel");
myComponent.render(
    <StrictMode>
        {/*<HotelCard2*/}
        {/*    imageUrl="https://example.com/hotel.jpg"*/}
        {/*    hotelName={data}*/}
        {/*    agencies={[*/}
        {/*        { name: 'Booking.com', url: 'https://booking.com/example', price: 120 },*/}
        {/*        { name: 'Expedia', url: 'https://expedia.com/example', price: 110 },*/}
        {/*    ]}*/}
        {/*    stars={4}*/}
        {/*    location="New York, NY"*/}
        {/*/>*/}
        {/*<HotelCard2*/}
        {/*    imageUrl="https://example.com/hotel.jpg"*/}
        {/*    hotelName="Example Hotel"*/}
        {/*    agencies={[*/}
        {/*        { name: 'Booking.com', url: 'https://booking.com/example', price: 120 },*/}
        {/*        { name: 'Expedia', url: 'https://expedia.com/example', price: 110 },*/}
        {/*    ]}*/}
        {/*    stars={4}*/}
        {/*    location="New York, NY"*/}
        {/*/>*/}
    </StrictMode>
);
