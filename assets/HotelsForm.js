import React from "react";
import ReactDOM from "react-dom";
import {StrictMode} from "react";
import HotelsForm from "./react/controllers/HotelsForm";

const myComponent = ReactDOM.createRoot(document.getElementById('HotelsForm'));
myComponent.render(
    <StrictMode>
        <HotelsForm />
    </StrictMode>
);
