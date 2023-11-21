import Petrinet from "./petrinet/petrinet";
import TokenCount from "./tokens/token-count";
import IntegerTokenCount from "./tokens/integer-token-count";

import HashTable from "src/hash-table/hash-table";

export default class Marking {
    protected map: HashTable<string, TokenCount>;

    public constructor(petrinet: Petrinet, map: HashTable<string, TokenCount> | null = null) {
        if (map !== null) {
            this.map = map;
        } else {
            this.map = new HashTable<string, TokenCount>();
        }
        let places = petrinet.places.toArray();
        for(let i = 0; i < places.length; i++) {
            this.map.set(places[i], new IntegerTokenCount(0));
        }
    }

    public set(place: string, tokens: TokenCount): void {
        this.map.set(place, tokens);
    }

    public get(place: string): TokenCount {
        if (this.map.has(place)) {
            return this.map.get(place)!;
        }
        throw new Error(`Could not get tokens for place ${place}`);
    }

    public get places(): string[] {
        return this.map.keys;
    }

    public static equals(lhs: Marking, rhs: Marking): boolean {
        let lplaces = lhs.places.sort();
        let rplaces = rhs.places.sort();
        if (lplaces.length === rplaces.length) {
            for(let i = 0; i < lplaces.length; i++) {
                let lplace = lplaces[i];
                let rplace = rplaces[i];
                if (lplace !== rplace || !lhs.get(lplace).equals(rhs.get(rplace))) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public toString(type: MarkingStringType = MarkingStringType.MINIMAL): string {
        let result = new Array<string>();
        if (type === MarkingStringType.FULL) {
            this.map.each((place: string, tokens: TokenCount) => {
                result.push(`${place}: ${tokens.toString()}`);
            });
        } else if (type === MarkingStringType.MINIMAL) {
            this.map.each((place: string, tokens: TokenCount) => {
                if (!(tokens instanceof IntegerTokenCount) || tokens.value > 0) {
                    result.push(`${place}: ${tokens.toString()}`);
                }
            });
        }
        return `${result.join(", ")}`;
    }
}

enum MarkingStringType {
    MINIMAL = 1,
    FULL    = 2,
}

export { Marking, MarkingStringType };
