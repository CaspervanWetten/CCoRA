class URLGenerator
{
    public Base : string;
    public constructor(base : string)
    {
        this.Base = base;
        if(this.Base[this.Base.length - 1] != "/"){
            this.Base += "/";
        }
    }
    
    public GetURL(...args : string[])
    {
        let res = args.join("/");
        return this.Base + res;
    }

    public static GetURL(base : string, ...args : string[] )
    {
        let gen = new URLGenerator(base);
        return gen.GetURL(...args);
    }
}
