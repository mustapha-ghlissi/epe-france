import React, {Component, Fragment} from 'react';
import ReactDOM from "react-dom";
import Footer from "./components/Footer";
import Header from "./components/Header";
import SearchForm from "./components/SearchForm";
import axios from 'axios';
import ReactSlider from "./components/ReactSlider";


class Home extends Component {

    constructor(props) {
        super(props);

        this.state = {
            data: null,
            isLoading: false
        }
    }

    componentDidMount() {
        this.setState({
            isLoading: false
        }, this.fetchData);
    }

    fetchData = () => {
        this.setState({
            isLoading: true
        });
        axios.get('/app-elected-members', {
            headers: {'X-Requested-With': 'XMLHttpRequest'},
        }).then((response) => {
            const {data} = response;
            console.log(response);
            this.setState({
                isLoading: false,
                data
            })
        })
            .catch((error) => {
                console.log(error);
            })
            .then(() => {
            });
    }

    render() {
        const {
            isLoading,
            data
        } = this.state;

        if (!data || isLoading) {
            return (
                <div className="d-flex flex-column align-items-center justify-content-center vh-100">
                    <img src="build/images/logo_epe.86109c77.png" style={{height: 60}} alt=""/>
                    <div className="mt-3 spinner-grow text-danger" role="status">
                        <span className="sr-only">Loading...</span>
                    </div>
                </div>
            )
        }

        return (
            <Fragment>
                <Header/>
                <SearchForm/>
                <div id="electedRepresentatives">
                    <div className="bg-light">
                        <div className="container">
                            <div className="row">
                                <div className="col">
                                    <p className="text-center py-4">
                                    S’informer, suivre et évaluer vos élus de proximité, c’est adhérer <b>à une autre façon de participer à la vie démocratique.</b>
                                        <br/><br/>
                                        
                                        Accepter d’être évalué en tant qu’élu, c’est avant tout affirmer une <b>volonté de progrès</b>, 
                                        c’est accepter de faire le bilan de sa propre politique dans l'objectif de la corriger, 
                                        de l’améliorer et de l'adapter aux besoins exprimés par ses concitoyens. 
                                        C’est également faire renaître <b>un sentiment de confiance et de proximité</b> dans la gestion de la cité. 
                                    </p>
                                    <div className="d-none d-lg-block py-4 epe-categories">
                                        <ul className="list-inline m-0 mb-3 p-0 d-flex flex-row flex-wrap align-items-center justify-content-around">
                                            <li>
                                                <a href="#maire"
                                                   className="badge badge-danger badge-pill d-block p-3 m-1">
                                                    Maires
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#areaPresident"
                                                   className="badge badge-success badge-pill d-block p-3 m-1"
                                                   style={{backgroundColor: '#916319'}}>
                                                    Présidents de Régions
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#senators"
                                                   className="badge badge-warning badge-pill d-block p-3 m-1"
                                                   style={{backgroundColor: '#F5871F'}}>
                                                    Sénateurs
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#communityAdvisors"
                                                   className="badge badge-primary badge-pill d-block p-3 m-1">
                                                    Conseillers Communautaires
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#corsicanAdvisors"
                                                   className="badge badge-warning badge-pill d-block p-3 m-1"
                                                   style={{backgroundColor: '#7F00FF'}}>
                                                    Conseillers Corse
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#member"
                                                   className="badge badge-light badge-pill d-block p-3 m-1"
                                                   style={{backgroundColor: '#77B5FE'}}>
                                                    Députés
                                                </a>
                                            </li>
                                        </ul>
                                        <ul className="list-inline m-0 p-0 d-flex flex-row flex-wrap align-items-center justify-content-around">
                                            <li>
                                                <a href="#departmentCouncillors"
                                                   className="badge badge-dark badge-pill d-block p-3 m-1">
                                                    Conseillers Départementaux
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#departmentPresident"
                                                   className="badge badge-info badge-pill d-block p-3 m-1">
                                                    Présidents de Départements
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#regionalCouncillors"
                                                   className="badge badge-secondary badge-pill d-block p-3 m-1">
                                                    Conseillers Régionaux
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#euroMembers"
                                                   className="badge badge-success badge-pill d-block p-3 m-1">
                                                    Députés Européens
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#municipalCouncillors"
                                                   className="badge badge-warning badge-pill d-block p-3 m-1">
                                                    Conseillers Municipaux
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="section" id="maire">
                        <div className="container">
                            <div className="row">
                                <div className="col">
                                    <div
                                        className="text-center d-flex flex-row align-items-center justify-content-center">
                                        <h3 className="section-title">
                                            Maires
                                        </h3>
                                    </div>
                                    <ReactSlider data={data} categoryId={9}/>
                                    <div className="text-center mt-5">
                                        <form
                                            action="/liste-des-maires"
                                            method="post">
                                            <input type="hidden" name="categoryId" value="9"/>
                                            <button className="btn btn-lg btn-link rounded-pill btn-more">
                                                Afficher <i className="fa fa-plus-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="section" id="departmentPresident">
                        <div className="container">
                            <div className="row">
                                <div className="col">
                                    <div
                                        className="text-center d-flex flex-row align-items-center justify-content-center">
                                        <h3 className="section-title">
                                            Présidents de Départements
                                        </h3>
                                    </div>
                                    <ReactSlider data={data} categoryId={10}/>
                                    <div className="text-center mt-5">
                                        <form
                                            action="/liste-des-presidents-des-departements"
                                            method="post">
                                            <input type="hidden" name="categoryId" value="10"/>
                                            <button className="btn btn-lg btn-link rounded-pill btn-more">
                                                Afficher <i className="fa fa-plus-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="section" id="areaPresident">
                        <div className="container">
                            <div className="row">
                                <div className="col">
                                    <div
                                        className="text-center d-flex flex-row align-items-center justify-content-center">
                                        <h3 className="section-title">
                                            Présidents de Régions
                                        </h3>
                                    </div>
                                    <ReactSlider data={data} categoryId={11}/>
                                    <div className="text-center mt-5">
                                        <form
                                            action="/liste-des-presidents-des-regions"
                                            method="post">
                                            <input type="hidden" name="categoryId" value="11"/>
                                            <button className="btn btn-lg btn-link rounded-pill btn-more">
                                                Afficher <i className="fa fa-plus-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="section" id="member">
                        <div className="container">
                            <div className="row">
                                <div className="col">
                                    <div
                                        className="text-center d-flex flex-row align-items-center justify-content-center">
                                        <h3 className="section-title">
                                            Députés
                                        </h3>
                                    </div>

                                    <ReactSlider data={data} categoryId={8}/>

                                    <div className="text-center mt-5">
                                        <form
                                            action="/liste-des-deputes"
                                            method="post">
                                            <input type="hidden" name="categoryId" value="8"/>
                                            <button className="btn btn-lg btn-link rounded-pill btn-more">
                                                Afficher <i className="fa fa-plus-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="section" id="municipalCouncillors">

                        <div className="container">
                            <div className="row">
                                <div className="col">
                                    <div
                                        className="text-center d-flex flex-row align-items-center justify-content-center">
                                        <h3 className="section-title">
                                            Conseillers Municipaux
                                        </h3>
                                    </div>

                                    <ReactSlider data={data} categoryId={1}/>

                                    <div className="text-center mt-5">
                                        <form
                                            action="/liste-des-conseillers-municipaux"
                                            method="post">
                                            <input type="hidden" name="categoryId" value="1"/>
                                            <button className="btn btn-lg btn-link rounded-pill btn-more">
                                                Afficher <i className="fa fa-plus-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="section" id="departmentCouncillors">
                        <div className="container">
                            <div className="row">
                                <div className="col">
                                    <div
                                        className="text-center d-flex flex-row align-items-center justify-content-center">
                                        <h3 className="section-title">
                                            Conseillers Départementaux
                                        </h3>
                                    </div>
                                    <ReactSlider data={data} categoryId={3}/>
                                    <div className="text-center mt-5">
                                        <form
                                            action="/liste-des-conseillers-departementaux"
                                            method="post">
                                            <input type="hidden" name="categoryId" value="3"/>
                                            <button className="btn btn-lg btn-link rounded-pill btn-more">
                                                Afficher <i className="fa fa-plus-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="section" id="regionalCouncillors">
                        <div className="container">
                            <div className="row">
                                <div className="col">

                                    <div
                                        className="text-center d-flex flex-row align-items-center justify-content-center">
                                        <h3 className="section-title">
                                            Conseillers Régionaux
                                        </h3>
                                    </div>

                                    <ReactSlider data={data} categoryId={4}/>

                                    <div className="text-center mt-5">
                                        <form
                                            action="/liste-des-conseillers-regionaux"
                                            method="post">
                                            <input type="hidden" name="categoryId" value="4"/>
                                            <button className="btn btn-lg btn-link rounded-pill btn-more">
                                                Afficher <i className="fa fa-plus-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="section" id="euroMembers">
                        <div className="container">
                            <div className="row">
                                <div className="col">
                                    <div
                                        className="text-center d-flex flex-row align-items-center justify-content-center">
                                        <h3 className="section-title">
                                            Députés Européens
                                        </h3>
                                    </div>

                                    <ReactSlider data={data} categoryId={6}/>

                                    <div className="text-center mt-5">
                                        <form
                                            action="/liste-des-deputes-europeens"
                                            method="post">
                                            <input type="hidden" name="categoryId" value="6"/>
                                            <button className="btn btn-lg btn-link rounded-pill btn-more">
                                                Afficher <i className="fa fa-plus-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="section" id="senators">
                        <div className="container">
                            <div className="row">
                                <div className="col">
                                    <div
                                        className="text-center d-flex flex-row align-items-center justify-content-center">
                                        <h3 className="section-title">
                                            Sénateurs
                                        </h3>
                                    </div>

                                    <ReactSlider data={data} categoryId={7}/>

                                    <div className="text-center mt-5">
                                        <form
                                            action="/liste-des-senateurs"
                                            method="post">
                                            <input type="hidden" name="categoryId" value="7"/>
                                            <button className="btn btn-lg btn-link rounded-pill btn-more">
                                                Afficher <i className="fa fa-plus-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="section" id="corsicanAdvisors">
                        <div className="container">
                            <div className="row">
                                <div className="col">
                                    <div
                                        className="text-center d-flex flex-row align-items-center justify-content-center">
                                        <h3 className="section-title">
                                            Conseillers Corse
                                        </h3>
                                    </div>

                                    <ReactSlider data={data} categoryId={5}/>

                                    <div className="text-center mt-5">
                                        <form
                                            action="/liste-des-conseillers-corse"
                                            method="post">
                                            <input type="hidden" name="categoryId" value="5"/>
                                            <button className="btn btn-lg btn-link rounded-pill btn-more">
                                                Afficher <i className="fa fa-plus-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="section" id="communityAdvisors">
                        <div className="container">
                            <div className="row">
                                <div className="col">
                                    <div
                                        className="text-center d-flex flex-row align-items-center justify-content-center">
                                        <h3 className="section-title">
                                            Conseillers Communautaires
                                        </h3>
                                    </div>

                                    <ReactSlider data={data} categoryId={2}/>

                                    <div className="text-center mt-5">
                                        <form
                                            action="/liste-des-conseillers-communautaires"
                                            method="post">
                                            <input type="hidden" name="categoryId" value="2"/>
                                            <button className="btn btn-lg btn-link rounded-pill btn-more">
                                                Afficher <i className="fa fa-plus-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <Footer/>
            </Fragment>
        )
    }
}


ReactDOM.render(<Home/>, document.getElementById('homepage'));