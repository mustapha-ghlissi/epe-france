import React, {Component, Fragment} from 'react';
import { string, object } from 'yup';
import axios from "axios";
import ReCAPTCHA from "react-google-recaptcha";
import ReactDOM from "react-dom";

let schema = object().shape({
    fullName: string().required('Champ requis'),
    email: string().email('Adresse email invalide').required('Champ requis'),
    subject: string().required('Champ requis'),
    message: string().required('Champ requis')
});

class Contact extends Component {

    constructor(props) {
        super(props);

        this.state = {
            isLoading: false,
            contactForm: {
                fullName: '',
                email: '',
                subject: '',
                message: '',
                recaptcha: false
            },
            formErrors: {},
            flashMessage: null,
        }
    }

    onSend = (e) => {
        const {contactForm} = this.state, formErrors = {};
        e.preventDefault();

        this.setState({
            isLoading: true
        });
        
        schema.validate(contactForm, {abortEarly: false}).then(value => {
            if(!contactForm.recaptcha) {
                formErrors['recaptcha'] = 'Veuillez valider le recaptcha'
                
                this.setState({
                    formErrors,
                    isLoading: false
                });

                return false;
            }

            axios.post('/contactez-nous', contactForm, {
                headers: {'X-Requested-With': 'XMLHttpRequest'},
            })
                .then( (response) => {
                    this.setState({
                        isLoading: false,
                        contactForm: {
                            fullName: '',
                            email: '',
                            subject: '',
                            message: '',
                            recaptcha: false
                        },
                        flashMessage: response.data.message,
                        formErrors: {}
                    })
                })
                .catch( (error) => {
                    console.log(error);
                });
        }).catch(({inner}) => {
            for(let error of inner) {
                formErrors[error.path] = error.message;
            }

            if(!contactForm.recaptcha) {
                formErrors['recaptcha'] = 'Veuillez valider le recaptcha'
            }

            this.setState({
                formErrors,
                isLoading: false
            });
        });
    }

    onTextChange = ({target}) => {
        this.setState((state) => ({
            contactForm: {
                ...state.contactForm,
                [target.name]: target.value
            }
        }))
    }

    onChangeCaptcha = (value) => {
        this.setState((state) => ({
            contactForm: {
                ...state.contactForm,
                recaptcha: true
            }
        }))
    }

    render() {

        const {isLoading, formErrors, flashMessage, contactForm} = this.state;

        return (
            <form action="" onSubmit={this.onSend}>
                {
                    flashMessage &&
                    <div className="alert alert-success alert-dismissible fade show" role="alert">
                        <button type="button" className="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            <span className="sr-only">Close</span>
                        </button>
                        {flashMessage}
                    </div>
                }
                <div className="row">
                    <div className="col-lg-6">
                        <div className="form-group">
                            <label htmlFor="fullName">Nom et prénom <sup className='text-danger'>*</sup></label>
                            <input type="text" name={'fullName'} value={contactForm.fullName} className={`form-control ${'fullName' in formErrors && 'is-invalid'}`} onChange={this.onTextChange}/>
                            {
                                'fullName' in formErrors &&
                                <div className="invalid-feedback">
                                    {formErrors.fullName}
                                </div>
                            }
                        </div>
                    </div>
                    <div className="col-lg-6">
                        <div className="form-group">
                            <label htmlFor="email">Adresse e-mail <sup className='text-danger'>*</sup></label>
                            <input type="email" name={'email'} value={contactForm.email} className={`form-control ${'email' in formErrors && 'is-invalid'}`} onChange={this.onTextChange}/>
                            {
                                'email' in formErrors &&
                                <div className="invalid-feedback">
                                    {formErrors.email}
                                </div>
                            }
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="form-group">
                            <label htmlFor="subject">Sujet <sup className='text-danger'>*</sup></label>
                            <input type="text" name={'subject'} value={contactForm.subject} className={`form-control ${'subject' in formErrors && 'is-invalid'}`} onChange={this.onTextChange}/>
                            {
                                'subject' in formErrors &&
                                <div className="invalid-feedback">
                                    {formErrors.subject}
                                </div>
                            }
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="form-group">
                            <label htmlFor="message">Message <sup className='text-danger'>*</sup></label>
                            <textarea name="message" id="message" value={contactForm.message} cols="30" rows="10" className={`form-control ${'message' in formErrors && 'is-invalid'}`} onChange={this.onTextChange}></textarea>
                            {
                                'message' in formErrors &&
                                <div className="invalid-feedback">
                                    {formErrors.message}
                                </div>
                            }
                        </div>
                    </div>
                    
                    <div className="col-12">
                        <div className='form-group'>
                        <ReCAPTCHA
                            className='mb-3'
                            sitekey="6Lf1gM0ZAAAAAP8tlTdEq0rWsIQ3JteELwQ05KvZ"
                            onChange={this.onChangeCaptcha}
                        />
                            {
                                'recaptcha' in formErrors &&
                                <small className="text-danger">
                                    {formErrors.recaptcha}
                                </small>
                            }
                        </div>
                    </div>
                    <div className="col-12">

                        <div className="form-group">
                            <button className='btn btn-primary btn-lg btn-block d-flex flex-row align-items-center justify-content-center' disabled={isLoading}>
                                {
                                    isLoading ?
                                        <Fragment>
                                            <span className="spinner-grow spinner-grow-lg mr-3" role="status" aria-hidden="true"></span>
                                            Envoi en cours ...
                                        </Fragment>:
                                        'Envoyer'
                                }
                            </button>
                        </div>

                        <div className="form-group">
                            <p>
                                NB: Les champs en (*) sont obligatoires.
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        );
    }
}

ReactDOM.render(<Contact/>, document.getElementById('contact'));