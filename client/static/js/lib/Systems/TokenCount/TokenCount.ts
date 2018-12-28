abstract class TokenCount
{
    public abstract Add(a : number | TokenCount) : TokenCount;
    public abstract Subtract(a : number | TokenCount) : TokenCount;
    public abstract ToString() : string;
}