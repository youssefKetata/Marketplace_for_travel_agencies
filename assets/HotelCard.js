import React from "react";
import ReactDOM from "react-dom/client";
import {StrictMode} from "react";
import HotelCard from "./react/controllers/HotelCard";

const myComponent = ReactDOM.createRoot(document.getElementById('HotelCard'));
const element = document.getElementById("HotelCard");
const data = element.getAttribute("data-hotel");
myComponent.render(
    <StrictMode>
        <HotelCard
            imageUrl="https://example.com/hotel.jpg"
            hotelName={data}
            agencies={[
                { name: 'Booking.com', url: 'https://booking.com/example', price: 120 },
                { name: 'Expedia', url: 'https://expedia.com/example', price: 110 },
            ]}
            stars={4}
            location="New York, NY"
        />
        <HotelCard
            imageUrl="https://example.com/hotel.jpg"
            hotelName="Example Hotel"
            agencies={[
                { name: 'Booking.com', url: 'https://booking.com/example', price: 120 },
                { name: 'Expedia', url: 'https://expedia.com/example', price: 110 },
            ]}
            stars={4}
            location="New York, NY"
        />
    </StrictMode>
);
