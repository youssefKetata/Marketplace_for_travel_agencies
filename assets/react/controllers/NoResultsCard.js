import React from "react";

export default function NoResultsCard() {
    return (
        <div className="noResult-card">
            <div className="noResult-card-body">
                {/* add image */}
                <img src = "./images/noResults.svg" alt = "no results found" />
                <h5 className="noResult-card-title">No Results</h5>
                <p className="noResult-card-text">Sorry, no results were found!</p>
            </div>
        </div>

    )
}