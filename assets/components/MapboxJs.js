import React, { useRef, useEffect, useState } from 'react';

import mapboxgl from '!mapbox-gl'; // eslint-disable-line import/no-webpack-loader-syntax

mapboxgl.accessToken = 'pk.eyJ1IjoibWFobW91ZGdhIiwiYSI6ImNsN2o5YXZhNDB3NTIzb3BiYm5wNzNiOW8ifQ.O5iDbbhwAtQFaxA7yCiPEQ';


export default function MapboxJs(props) {
    const id = props.data_id;
    const longitude = id.getAttribute('long');
    const laltitude = id.getAttribute('lalt');


    const mapContainer = useRef(null);
    const map = useRef(null);

    const [lng, setLng] = useState((longitude) ? longitude : 9.644374211098256);
    const [lat, setLat] = useState((laltitude) ? laltitude : 34.06381565252011 );



    const [zoom, setZoom] = useState(5);



    useEffect(() => {
        if (map.current) return; // initialize map only once
        map.current = new mapboxgl.Map({
            container: mapContainer.current,
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [lng, lat],
            zoom: zoom
        });
        map.current.addControl(new mapboxgl.NavigationControl(), "top-right");
        var marker1 = new mapboxgl.Marker({ color: 'red' })
            .setLngLat([lng, lat])
            .addTo(map.current);
    });
    return (
        <div>
            <div className="sidebar">
                Longitude: {lng} | Latitude: {lat} | Zoom: {zoom}
            </div>
            <div ref={mapContainer} className="map-container" />
        </div>
    );
}