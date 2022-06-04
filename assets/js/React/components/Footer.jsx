import React, {Component} from 'react';
import moment from 'moment';
import _ from 'lodash';

class Footer extends Component {
    render() {
        return (
            <footer>
                <div className="bg-primary py-3">
                    <div className="container">
                        <div className="row">
                            <div className="col-lg-4">
                                <h3 className="font-weight-bolder text-white">
                                    EPE FRANCE
                                </h3>
                                <p>L’application qui défend et qui revitalise la vie démocratique, au service du peuple, pour le peuple, par le peuple et avec le peuple.</p>
                                <ul className="list-unstyled mb-3">
                                    <li>
                                        <a href="{{ path('app_contact') }}">Contactez-nous</a>
                                    </li>
                                </ul>
                                <ul className="list-unstyled">
                                    <li className="d-flex flex-row align-items-center p-2">
                                        <i className="fa fa-at"></i> <span
                                        className="ml-3">contact@epe-france.com</span>
                                    </li>
                                    <li className="d-flex flex-row align-items-center p-2">
                                        <i className="fa fa-phone-alt"></i> <span className="ml-3">+ 33 (0) 6 12 64 51 95</span>
                                    </li>
                                    <li className="d-flex flex-row align-items-center p-2">
                                        <i className="fa fa-map-marker-alt"></i> <span className="ml-3">
                                37 rue des Mathurins, 75008 Paris (France)
                            </span>
                                    </li>
                                </ul>

                                <div className="my-2">
                                    <h5>
                                        Suivez nous
                                    </h5>
                                    <ul className="list-unstyled d-inline-flex justify-content-center align-items-center">
                                        <li className="px-1">
                                            <a href="https://www.facebook.com/%C3%89valuation-de-la-politique-des-%C3%A9lus-httpswwwepe-francecom-109781250890268" target="_blank">
                                                <i className="fab fa-facebook-square fa-2x text-white"></i>
                                            </a>
                                        </li>
                                        <li className="px-1">
                                            <a href="https://www.instagram.com/epe2346/" target="_blank">
                                                <i className="fab fa-instagram-square fa-2x text-white"></i>
                                            </a>
                                        </li>
                                        <li className="px-1">
                                            <a href="https://twitter.com/eusepfrance" target="_blank">
                                                <i className="fab fa-twitter-square fa-2x text-white"></i>
                                            </a>
                                        </li>
                                        <li className="px-1">
                                            <a href="https://www.linkedin.com/company/70870248/admin/" target="_blank">
                                                <i className="fab fa-linkedin fa-2x text-white"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div className="col-lg-4">
                                <h4 className="footer-title-block">
                                    Liens utiles
                                </h4>
                                <hr className="bg-light"/>
                                <ul className="list-unstyled epe-categories">
                                    <li><a href="https://legifrance.gouv.fr/" target="_blank">Légifrance</a></li>
                                    <li><a href="https://impots.gouv.fr/" target="_blank">Impots France</a></li>
                                    <li><a href="https://interieur.gouv.fr/" target="_blank">Intérieur</a></li>
                                    <li><a href="https://service-public.fr/" target="_blank">Service public</a></li>
                                    <li><a href="https://data.gouv.fr/" target="_blank">Données</a></li>
                                    <li><a href="https://gouvernement.fr/" target="_blank">Gouvernement</a></li>
                                    <li><a href="https://www.france.fr/fr" target="_blank">France</a></li>
                                    <li><a href="https://economie.gouv.fr/" target="_blank">Economie</a></li>
                                    <li><a href="https://amendes.gouv.fr/tai" target="_blank">Amendes</a></li>
                                    <li><a href="https://www.assemblee-nationale.fr/" target="_blank">Assemblée
                                        nationale</a></li>
                                    <li><a href="http://www.senat.fr/" target="_blank">Senat</a></li>
                                    <li><a href="https://cadastre.gouv.fr/" target="_blank">Cadastre</a></li>
                                </ul>
                            </div>
                            <div className="col-lg-4">
                                <h4 className="footer-title-block">
                                    Nos apps
                                </h4>
                                <hr className="bg-light"/>


                                <ul className="list-unstyled mb-5 d-flex flex-row justify-content-center">
                                    <li className="pr-1">
                                        <a href="https://play.google.com/store/apps/details?id=com.epe.france" target="_blank" className="d-block">
                                            <img className="img-fluid"
                                                 src="build/images/google-play.070a0908.png"
                                                 style={{ height: 50}}/>
                                        </a>
                                    </li>
                                    <li className="pl-1">
                                        <a href="https://apps.apple.com/us/app/epe-france/id1538631943" target="_blank" className="d-block">
                                            <img className="img-fluid"
                                                 src="build/images/apple-store.ff14d536.png"
                                                 style={{height: 50}}/>
                                        </a>
                                    </li>
                                </ul>

                                <h4 className="footer-title-block">
                                    Nos marques
                                </h4>
                                <hr className="bg-light"/>
                                <ul className="list-unstyled d-flex flex-row justify-content-center align-items-center">
                                    <li className="flex-fill bg-light rounded mr-1">
                                        <a href="https://eusep-france.com/" target="_blank"
                                           className="p-2 d-block text-center">
                                            <img className="img-fluid"
                                                 src="build/images/eusep-logo.6227695c.png"
                                                 style={{height: 60}}/>
                                        </a>
                                    </li>
                                    <li className="flex-fill bg-light rounded ml-1">
                                        <a href="https://www.rif-france.com/" target="_blank"
                                           className="p-2 d-block text-center">
                                            <img className="img-fluid"
                                                 src="build/images/logo-rif.f36fc939.png"
                                                 style={{ height: 60 }}/>
                                        </a>
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
                <div className="copyright text-center py-3">
                    <ul className="list-unstyled p-0 m-0 d-inline-flex flex-wrap align-items-center justify-content-center">
                        <li className="p-2"><a href="/a-propos">{ _.toUpper('à')} propos de nous</a>
                        </li>
                        -
                        <li className="p-2"><a href="/conditions-générales">CGU</a></li> -
                        <li className="p-2"><a href="/rgpd">RGPD</a></li> -
                        <li className="p-2"><a href="/mentions-légales">Mentions légales</a></li> -
                        <li className="p-2"><a href="/plan-du-site">Plan du site</a></li>
                    </ul>
                    <br/>
                    <small>Tous droits réservés &copy; 2019 - {moment().format('YYYY')} - Association pour la défense
                        des fonctionnaires et des usagers de la fonction publique</small>
                </div>
            </footer>
        );
    }
}

export default Footer;
