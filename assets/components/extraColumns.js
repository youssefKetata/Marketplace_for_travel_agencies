import React from "react";

export default function DropDownColumn(actions, action_route) {
    return (
        {
            button: true,
            width: '10',
            name: 'ACTIONS',
            cell: row =>
                (
                    <div className="App">
                        <div class="openbtn text-center">
                            {/*<button*/}
                            {/*    type="button"*/}
                            {/*    class="btn btn-primary"*/}
                            {/*    data-bs-toggle="modal"*/}
                            {/*    data-bs-target="#myModal"*/}
                            {/*>*/}
                            {/*    Open modal*/}
                            {/*</button>*/}

                            <div className="dropdown">
                                <button className="btn btn-secondary dropdown-toggle" type="button"
                                        id="dropdownMenuButton1"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                </button>
                                <ul className="dropdown-menu" aria-labelledby="dropdownMenuButton1">


                                    {actions.map((action, i) => {
                                        return (
                                            <li>

                                                <a className="dropdown-item" href={action_route+'/'+row.id+action['action_link']}>
                                                    {action['action_label']}
                                                </a>
                                            </li>
                                        )
                                    })}
                                </ul>
                            </div>


                            <div class="modal" tabindex="-1" id="myModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Modal title</h5>
                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"
                                                aria-label="Close"
                                            ></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Modal body text goes here.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button
                                                type="button"
                                                class="btn btn-secondary"
                                                data-bs-dismiss="modal"
                                            >
                                                Close
                                            </button>
                                            <button type="button" class="btn btn-primary">
                                                Save changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )
        }
    )
}
