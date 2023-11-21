import HashSet from "src/hash-set/hash-set";

export default class Petrinet {
    protected _places: HashSet<string>;
    protected _transitions: HashSet<string>;

    public constructor(p: HashSet<string>, t: HashSet<string>) {
        this._places = p;
        this._transitions = t;
    }

    get places(): HashSet<string> {
        return this._places;
    }

    get transitions(): HashSet<string> {
        return this._transitions;
    }
}
