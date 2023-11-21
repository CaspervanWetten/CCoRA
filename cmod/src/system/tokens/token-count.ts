export default abstract class TokenCount {
    public abstract add(a: number | TokenCount): TokenCount;
    public abstract subtract(a: number | TokenCount): TokenCount;
    public abstract equals(rhs: TokenCount): boolean;
    public abstract toString(): string;
}
