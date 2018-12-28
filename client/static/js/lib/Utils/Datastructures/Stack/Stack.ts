class Stack<T>
{
    protected Top : StackNode<T> | undefined;
    
    public constructor()
    {
        this.Top = undefined;
    }
    
    public Push(x : T)
    {
        let node = new StackNode(x, this.Top);
        this.Top = node;
    }

    public Pop() : T
    {
        let res = undefined;
        if(this.Top){
            res = this.Top.Key;
            this.Top = this.Top.Prev;        
        }
        return res;
    }
    
    public IsEmpty()
    {
        return this.Top == undefined;
    }
}

class StackNode<T>
{
    public Key : T;
    public Prev : StackNode<T> | undefined;

    public constructor(key : T, prev : StackNode<T> | undefined)
    {
        this.Key = key;
        this.Prev = prev;
    }
}

enum VisitState{
    Unvisited,
    Tovisit,
    Visited
}