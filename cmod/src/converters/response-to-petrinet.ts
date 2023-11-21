import Petrinet from "src/system/petrinet/petrinet";
import { PetrinetResponse } from "src/types";

import HashSet from "src/hash-set/hash-set";

export default class ResponseToPetrinet {
    public static convert(response: PetrinetResponse) {
        let places = new HashSet<string>();
        let transitions = new HashSet<string>();

        for(let i = 0; i < response.places.length; i++) {
            places.add(response.places[i]);
        }

        for(let i = 0; i < response.transitions.length; i++) {
            transitions.add(response.transitions[i]);
        }

        let petrinet = new Petrinet(places, transitions);
        return petrinet;
    }
}
