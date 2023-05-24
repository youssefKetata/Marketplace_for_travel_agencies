import React, { useState, useEffect, useCallback } from 'react';

export default function FilterByStar(props) {
    //filter should only change pratialHotels in parent component
    const { availability } = props;//should not be changed
    let [hotels5, setHotest5] = useState(0);
    let [hotels4, setHotest4] = useState(0);
    let [hotels3, setHotest3] = useState(0);
    let [hotels2, setHotest2] = useState(0);
    let [hotels1, setHotest1] = useState(0);
    let [hotels0, setHotest0] = useState(0);
    let [isChecked5, setIsChecked5] = useState(false);
    let [isChecked4, setIsChecked4] = useState(false);
    let [isChecked3, setIsChecked3] = useState(false);
    let [isChecked2, setIsChecked2] = useState(false);
    let [isChecked1, setIsChecked1] = useState(false);
    let [isChecked0, setIsChecked0] = useState(false);
    // const [isChecked, setIsChecked] = [false, false, false, false, false, false];

    // const handleCheckBoxChange = useCallback((event, stars) => {
    //     setIsChecked(prevState => {
    //         const newState = [...prevState];
    //         newState[stars] = event.target.checked;
    //         return newState;
    //     });
    // },[]);
    //handle checkbox click
    const handleClick = (event, stars) => {
        switch (parseInt(stars)) {
            case 5:
                setIsChecked5(event.target.checked);
                break;
            case 4:
                setIsChecked4(event.target.checked);
                break;
            case 3:
                setIsChecked3(event.target.checked);
                break;
            case 2:
                setIsChecked2(event.target.checked);
                break;
            case 1:
                setIsChecked1(event.target.checked);
                break;
            case 0:
                setIsChecked0(event.target.checked);
                break;
            default:
                break;
        }
    }

    useEffect(() => {
        const starArray = []
        //put in starArray every star that is checked(number)
        if (isChecked5) starArray.push(5);
        if (isChecked4) starArray.push(4);
        if (isChecked3) starArray.push(3);
        if (isChecked2) starArray.push(2);
        if (isChecked1) starArray.push(1);
        if (isChecked0) starArray.push(0);
        //if no star is checked, put all stars in starArray
        if (!starArray.length) starArray.push(5, 4, 3, 2, 1, 0);
        props.setFilters(prev => {
            return [
                prev[0],
                starArray,
                prev[2]
            ]
        })

    }, [isChecked0, isChecked1, isChecked2, isChecked3, isChecked4, isChecked5])

    //count number of hotels for each category
    useEffect(() => {
        //iterate throught availibility and create key value pair where key is number
        //of stars and the value is number of hotels with that number of stars
        const counts = availability.reduce((acc, curr) => {
            const category = parseInt(curr.sellers[0].category);
            return { ...acc, [category]: (acc[category] || 0) + 1 };
        }, {});
        setHotest5(counts[5] || 0);
        setHotest4(counts[4] || 0);
        setHotest3(counts[3] || 0);
        setHotest2(counts[2] || 0);
        setHotest1(counts[1] || 0);
        setHotest0(counts[0] || 0);
    }, []);

    return (
        <div className="left_side_search_boxed">
            <div className="left_side_search_heading">
                <h5>Filter by hotel star</h5>
            </div>
            <div className="filter_review">
                <form className="review_star">
                    {hotels5 > 0 &&
                        <div className="form-check">
                            <input className="form-check-input" type="checkbox" value=""
                                   id="flexCheckDefaulta" checked={isChecked5} onChange={(event) => handleClick(event, 5)}></input>
                            <label className="form-check-label" htmlFor="flexCheckDefaulta">
                                <i className="fas fa-star color_theme"></i>
                                <i className="fas fa-star color_theme"></i>
                                <i className="fas fa-star color_theme"></i>
                                <i className="fas fa-star color_theme"></i>
                                <i className="fas fa-star color_theme"></i>
                            </label>
                            {hotels5}
                        </div>}
                    {hotels4 > 0 &&
                        <div className="form-check">
                            <input className="form-check-input" type="checkbox" value=""
                                   id="flexCheckDefaulf21" checked={isChecked4} onChange={(event) => handleClick(event, 4)}></input>
                            <label className="form-check-label" htmlFor="flexCheckDefaulf21">
                                <i className="fas fa-star color_theme"></i>
                                <i className="fas fa-star color_theme"></i>
                                <i className="fas fa-star color_theme"></i>
                                <i className="fas fa-star color_theme"></i>
                                <i className="fas fa-star color_asse"></i>
                            </label>
                            {hotels4}
                        </div>}
                    {hotels3 > 0 &&
                        <div className="form-check">
                            <input className="form-check-input" type="checkbox" value=""
                                   id="flexCheckDefaultf3" checked={isChecked3} onChange={(event) => handleClick(event, 3)}></input>
                            <label className="form-check-label" htmlFor="flexCheckDefaultf3">
                                <i className="fas fa-star color_theme"></i>
                                <i className="fas fa-star color_theme"></i>
                                <i className="fas fa-star color_theme"></i>
                                <i className="fas fa-star color_asse"></i>
                                <i className="fas fa-star color_asse"></i>
                            </label>
                            {hotels3}
                        </div>}
                    {hotels2 > 0 &&
                        <div className="form-check">
                            <input className="form-check-input" type="checkbox" value=""
                                   id="flexCheckDefaultf4" checked={isChecked2} onChange={(event) => handleClick(event, 2)}></input>
                            <label className="form-check-label" htmlFor="flexCheckDefaultf4">
                                <i className="fas fa-star color_theme"></i>
                                <i className="fas fa-star color_theme"></i>
                                <i className="fas fa-star color_asse"></i>
                                <i className="fas fa-star color_asse"></i>
                                <i className="fas fa-star color_asse"></i>
                            </label>{hotels2}
                        </div>}
                    {hotels1 > 0 &&
                        <div className="form-check">
                            <input className="form-check-input" type="checkbox" value=""
                                   id="flexCheckDefaultf5" checked={isChecked1} onChange={(event) => handleClick(event, 1)}></input>
                            <label className="form-check-label" htmlFor="flexCheckDefaultf5">
                                <i className="fas fa-star color_theme"></i>
                                <i className="fas fa-star color_asse"></i>
                                <i className="fas fa-star color_asse"></i>
                                <i className="fas fa-star color_asse"></i>
                                <i className="fas fa-star color_asse"></i>
                            </label>{hotels1}
                        </div>}
                    {hotels0 > 0 &&
                        <div className="form-check">
                            <input className="form-check-input" type="checkbox" value=""
                                   id="flexCheckDefaultf6" checked={isChecked0} onChange={(event) => handleClick(event, 0)}></input>
                            <label className="form-check-label" htmlFor="flexCheckDefaultf6">
                                <i className="fas fa-star color_asse"></i>
                                <i className="fas fa-star color_asse"></i>
                                <i className="fas fa-star color_asse"></i>
                                <i className="fas fa-star color_asse"></i>
                                <i className="fas fa-star color_asse"></i>
                            </label>{hotels0}
                        </div>
                    }
                </form>
            </div>
        </div>
    )
}