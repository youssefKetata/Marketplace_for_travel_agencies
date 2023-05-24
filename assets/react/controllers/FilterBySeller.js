import React, { useState, useEffect } from 'react';

export default function FilterBySeller(props) {
    //filter should only change pratialHotels in parent component
    const sellers = Object.entries(props.sellers);
    const [isChecked, setIsChecked] = useState(() => {
        const initialCheckedState = {};
        sellers.forEach(([key]) => {
            initialCheckedState[key] = false;
        });
        return initialCheckedState;
    });
    const handleClick = sellerId => {
        setIsChecked({
            ...isChecked,
            [sellerId]: !isChecked[sellerId]
        });
    }

    // listen to user click and update the partialHotels
    useEffect(() => {
        //check if at least one seller is checked
        if (Object.values(isChecked).every(value => value === false)) {
            //if no seller is checked, return all sellers
            props.setFilters(prev => {
                const initialCheckedState = {};
                sellers.forEach(([key]) => {
                    initialCheckedState[key] = true;
                });
                return [initialCheckedState,
                    prev[1],
                    prev[2]
                ];
            })
            return;
        }
        else {
            props.setFilters(prev => {
                return [
                    isChecked,
                    prev[1],
                    prev[2]
                ]
            })
        }
    }, [isChecked])

    return (
        <div className="left_side_search_boxed">
            <div className="left_side_search_heading">
                <h5>Filter by agency</h5>
            </div>
            <div className="filter_review">
                <form className="review_star">
                    {Object.values(sellers).map((seller, index) => {
                        return (
                            <div className="form-check" key={index}>
                                <input className="form-check-input" type="checkbox" style={{cursor: 'pointer'}}
                                       id={`checkBox_${index}`} checked={isChecked[seller[0]]} onChange={e => handleClick(seller[0])}></input>
                                <label className="form-check-label" htmlFor={`checkBox_${index}`} style={{cursor: 'pointer'}} >
                                    {seller[1].name}
                                </label>
                            </div>
                        )
                    })}
                </form>
            </div>
        </div>
    )
}