import React, { useState } from 'react';
import Hotels from './HotelsForm'; // Import the Hotels component
import Tours from './ToursForm'; // Import the Tours component
import Flights from './FlightsForm'; // Import the Flights component


const NavBar = (props) => {
    const [activeTab, setActiveTab] = useState('hotels'); // State to keep track of the active tab

    // Function to handle tab click
    const handleTabClick = (tab) => {
        setActiveTab(tab);
    };

    return (
        <section id="theme_search_form" >
            <div className="container">
                <div className="row">
                    <div className="col-lg-12">
                        <div className="theme_search_form_area">
                            <div className="theme_search_form_tabbtn">
                                <ul className="nav nav-tabs" role="tablist">
                                    <li onClick={() => handleTabClick('hotels')} className="nav-item" role="presentation">
                                        <button className="nav-link active" id="hotels-tab" data-bs-toggle="tab"
                                                data-bs-target="#hotels" type="button" role="tab" aria-controls="hotels"
                                                aria-selected="true"><i className="fas fa-hotel"></i>Hotels</button>
                                    </li>
                                    <li onClick={() => handleTabClick('flights')} className="nav-item" role="presentation">
                                        <button className="nav-link" id="flights-tab" data-bs-toggle="tab"
                                                data-bs-target="#flights" type="button" role="tab" aria-controls="flights"
                                                aria-selected="false"><i className="fas fa-plane-departure"></i>Flights</button>
                                    </li>
                                    <li onClick={() => handleTabClick('tours')} className="nav-item" role="presentation">
                                        <button className="nav-link" id="tours-tab" data-bs-toggle="tab" data-bs-target="#tours"
                                                type="button" role="tab" aria-controls="tours" aria-selected="false"><i
                                            className="fas fa-globe"></i>Tours</button>
                                    </li>
                                </ul>
                            </div>
                            <div className="tab-content" id="myTabContent">
                                {/* Render active component */}
                                {activeTab === 'hotels' && <Hotels onDataReceived = {props.onDataReceived }/>}
                                {activeTab === 'tours' && <Tours />}
                                {activeTab === 'flights' && <Flights />}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section >
    );
};

export default NavBar;
