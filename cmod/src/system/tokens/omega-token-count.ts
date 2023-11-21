import TokenCount from "./token-count";

export default class OmegaTokenCount extends TokenCount {
    public add(rhs: number | TokenCount): TokenCount {
        return new OmegaTokenCount();
    }

    public subtract(rhs: number | TokenCount): TokenCount {
        return new OmegaTokenCount();
    }

    public equals(rhs: TokenCount): boolean {
        if (rhs instanceof OmegaTokenCount) {
            return true;
        }
        return false;
    }

    public toString():string {
        return "ω";
    }
}
