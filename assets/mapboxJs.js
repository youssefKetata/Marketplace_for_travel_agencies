import React from 'react';
import ReactDOM from 'react-dom';
import 'mapbox-gl/dist/mapbox-gl.css';
import './styles/app.css';
import MapboxJs from "./components/MapboxJs";


const id_map =  document.getElementById('mapbox');

ReactDOM.render(
        <MapboxJs
        data_id ={id_map}/>,
    id_map
);