abstract class SystemElement
{
    protected Id : number;

    public constructor(id : number)
    {
        this.Id = id;
    }

    public GetId() 
    {
        return this.Id;
    }
}