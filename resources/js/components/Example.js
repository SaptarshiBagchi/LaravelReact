import ReactDOM from "react-dom";
import { useState } from "react";

function Example() {
    const [state, setstate] = useState(0);
    return (
        <div className="container">
            <div className="row justify-content-center">
                <div className="col-md-8">
                    <div className="card">
                        <div className="card-header">Example Component</div>

                        <div className="card-body">
                            I'm an example component!
                            <p>{state}</p>
                        </div>
                        <button onClick={() => setstate(state + 1)}>
                            Increment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Example;

if (document.getElementById("example")) {
    ReactDOM.render(<Example />, document.getElementById("example"));
}
