import TokenCount from "./token-count";
import OmegaTokenCount from "./omega-token-count";

export default class IntegerTokenCount extends TokenCount {
    public value: number;

    public constructor(value: number) {
        super();
        this.value = value;
    }

    public add(rhs: number | TokenCount): TokenCount {
        if(rhs instanceof IntegerTokenCount) {
            let value = this.value + rhs.value;
            return new IntegerTokenCount(value);
        } else if(rhs instanceof OmegaTokenCount) {
            return new OmegaTokenCount;
        } else if(typeof rhs === "number") {
            let value = this.value + (rhs as number);
            return new IntegerTokenCount(value);
        } else {
            return this;
        }
    }

    public subtract(rhs: number | TokenCount): TokenCount {
        if(rhs instanceof IntegerTokenCount) {
            let value = this.value - rhs.value;
            return new IntegerTokenCount(value);
        } else if(rhs instanceof OmegaTokenCount) {
            return new OmegaTokenCount;
        } else if(typeof rhs === "number") {
            let value = this.value - (rhs as number);
            return new IntegerTokenCount(value);
        } else {
            return this;
        }
    }

    public equals(rhs: TokenCount): boolean {
        if (rhs instanceof IntegerTokenCount) {
            return this.value === rhs.value;
        } else if (rhs instanceof OmegaTokenCount) {
            return false;
        }
        return false;
    }

    public toString(): string {
        return this.value.toString();
    }
}
