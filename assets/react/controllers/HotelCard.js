import React, { useState } from 'react';

//import hotelCard.css please
import './FlightsForm'
import './hotelCard.css'

const HotelCard = ({ imageUrl, hotelName, agencies, stars, location }) => {
    const [isHovered, setIsHovered] = useState(false);
    const lowestPrice = Math.min(...agencies.map((agency) => agency.price));
    imageUrl = 'https://picsum.photos/500/200';
    const renderPriceComparison = () => {
        if (agencies.length > 1) {
            return (
                <div className="price-comparison">
                    {agencies.map((agency) => (
                        <span key={agency.name}>
              {agency.name}: ${agency.price}
                            <br />
            </span>
                    ))}
                    <p>Lowest price: ${lowestPrice}</p>
                </div>
            );
        } else {
            return <p className="single-price">Price: ${lowestPrice}</p>;
        }
    };

    const handleMouseEnter = () => {
        setIsHovered(true);
    };

    const handleMouseLeave = () => {
        setIsHovered(false);
    };

    return (

        <div
            className="col-lg-12 col-md-6 col-sm-6 col-12"
            onMouseEnter={handleMouseEnter}
            onMouseLeave={handleMouseLeave}
        >
            <div className='hotel-card'>
                <img className="hotel-image" src="https://picsum.photos/500/200" alt={`${hotelName} Hotel`} />
                <div className="hotel-info">
                    <h2 className="hotel-name">{hotelName}</h2>
                    <p className="hotel-stars">{stars} stars</p>
                    <p className="hotel-location">{location}</p>
                    {renderPriceComparison()}
                    <a
                        className={`book-now-button ${isHovered ? 'book-now-button-hover' : ''
                        }`}
                        href={agencies[0].url}
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Book now
                    </a>
                </div>
            </div>
        </div>
    );
};

export default HotelCard;
