import React, {Component} from 'react';
import _ from 'lodash';

class Header extends Component {
    render() {
        return (
            <nav className="navbar navbar-expand-lg border-bottom fixed-top bg-light">
                <div className="container">
                    <a className="navbar-brand" href="/">
                        <img src="build/images/logo_epe.86109c77.png" style={{height: 90}}
                             className="img-fluid" alt="" />
                    </a>
                    <button className="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02"
                            aria-expanded="false" aria-label="Toggle navigation">
                        <i className="fas fa-bars text-primary"></i>
                    </button>

                    <div className="collapse navbar-collapse" id="navbarTogglerDemo02">
                        <ul className="navbar-nav d-none d-lg-flex ml-auto mt-2 mt-lg-0">
                            <li className="nav-item active">
                                <a className="nav-link rounded-pill ml-2 btn btn-primary text-white"
                                   href="/">Accueil <span className="sr-only">(current)</span></a>
                            </li>
                            <li className="nav-item">
                                <a className="nav-link rounded-pill ml-2 btn btn-primary text-white"
                                   href="/contactez-nous">Contactez-nous</a>
                            </li>
                            <li className="nav-item">
                                <a className="nav-link rounded-pill ml-2 btn btn-primary text-white"
                                   href="/a-propos">{ _.toUpper('à') } propos de nous</a>
                            </li>
                        </ul>

                        <ul className="navbar-nav d-flex d-lg-none align-items-center justify-content-center ml-auto mt-2 mt-lg-0">
                            <li className="nav-item active">
                                <a className="nav-link"
                                   href="/">Accueil <span
                                    className="sr-only">(current)</span></a>
                            </li>
                            <li className="nav-item">
                                <a className="nav-link" href="/contactez-nous">Contactez-nous</a>
                            </li>
                            <li className="nav-item">
                                <a className="nav-link" href="/a-propos">{ _.toUpper('à') } propos de
                                    nous</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        );
    }
}

export default Header;