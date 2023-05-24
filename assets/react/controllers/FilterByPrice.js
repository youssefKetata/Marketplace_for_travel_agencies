import React, { useState, useEffect } from 'react';
import Slider from '@mui/material/Slider';


export default function FilterByPrice(props) {
    const minDistance = 10;
    const minValue = props.minPrice;
    const maxValue = props.maxPrice;
    const [value1, setValue1] = useState([minValue, maxValue]);
    const handleChange1 = (event, newValue, activeThumb,) => {
        if (!Array.isArray(newValue)) {
            return;
        }

        if (activeThumb === 0) {
            setValue1([Math.min(newValue[0], value1[1] - minDistance), value1[1]]);
        } else {
            setValue1([value1[0], Math.max(newValue[1], value1[0] + minDistance)]);
        }
    };

    function valuetext(value) {
        return `${value}`;
    }

    const handleSliderChangeCommitted = (event, newValue, activeThumb) => {
        // Update the filters when the mouse button is released
        props.setFilters(prev => {
            return [
                prev[0],
                prev[1],
                value1
            ]
        });
    };




    return (
        <div className="left_side_search_boxed" >
            <div className="left_side_search_heading">
                <h5>Filter by price</h5>
            </div>

            <div className="filter_review"  style={{padding: "10px 10px 0 10px"}}>
                <form className="review_star">
                    <Slider
                        style={{color: '#8b3eea', paddingTop: '30px'}}
                        getAriaLabel={() => 'Minimum distance'}
                        value={value1}
                        onChange={handleChange1}
                        onChangeCommitted={handleSliderChangeCommitted}
                        valueLabelDisplay="on"
                        getAriaValueText={valuetext}
                        step={10}
                        disableSwap
                        min={minValue}
                        max={maxValue}
                    />
                </form>
            </div>
        </div>
    )
}