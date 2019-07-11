import React from 'react';
import ReactDOM from 'react-dom';
import './index.css';
import cn from 'classnames';
import axios from  'axios';
import Chat from "./Chat";


class Login extends React.Component {
    constructor(props) {
        const auth = JSON.parse(localStorage.getItem('token')) || {};
        super(props);
        this.state = {
            username: '',
            password: '',
            validations: {

            },
            token: auth.token ||''

        };

    }

    handleChange = (e) => {
        const {name, value} = e.target;
        this.setState({[name]: value});
    }


    handleSubmit = (e) => {
        e.preventDefault();
        const validations = this.validate(this.state);
        if (Object.keys(validations).length){

            this.setState({
                validations
            })

        } else {
            this.ascForToken(this.state.username,this.state.password);

        }


    };


    validate(state){
        const validations = {};


        if(state.username.length < 3){
            validations['username']  = true;
        }

        if(state.password.length < 3){
            validations['password']  = true;
        }

        return validations;
    }

    ascForToken(username,password){
        axios.post('http://localhost/api/login', {
            login: username,
            password: password,
        }, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        }).then((response) => {

            this.setState(
                response.data
            );

            localStorage.setItem('token', JSON.stringify(response.data))

        })
            .catch((reason) =>{
                this.setState(
                    reason.response.data
                );
            });

    }

    render() {
        if (this.state.token){
            return <Chat token={this.state.token}/>
        }
        return (
            <div className="content">

                <form className="form" action="">
                    <div className="form-row"><h1>Login Page</h1></div>
                    <div className="form-row">Username:

                        <input
                            className={cn('form-input-text', { invalid: this.state.validations.username })}
                            type="text"
                            name="username"
                            value={this.state.username}
                            onChange={this.handleChange}
                        />

                    </div>
                    <div className="form-row">Password:
                        <input
                            className={cn('form-input-text', { invalid: this.state.validations.password })}
                            type="password"
                            name="password"
                            value={this.state.password}
                            onChange={this.handleChange}
                        />
                        <div
                            className={cn({pass_hint_none: !(this.state.wrong)},{pass_hint_visible: this.state.wrong})}
                        >incorrect password</div>
                    </div>
                    <div className="form-row">
                        <button
                            className="form-input-submit"
                            value="Login"
                            onClick={this.handleSubmit}
                        >Login</button>
                    </div>
                </form>

            </div>
        );
    }
}

// ========================================

ReactDOM.render(
    <Login/>,
    document.getElementById('root')
);
