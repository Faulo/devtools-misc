using System.Collections.Generic;
using System.Linq;
using NUnit.Framework;
using Ulisses.Core.Utilities.Editor;

namespace Ulisses.Sandbox.Tests {
    [TestFixture]
    [Category("Ulisses.Sandbox.PackagesWIPAssets")]
    internal sealed class PackagesWIPAssetValidation : AssetValidationBase<PackagesWIPAssetValidation.AssetSource> {
        internal sealed class AssetSource : AssetSourceBase {
            protected override string ValidateAssetsInDirectory => "Packages";
            protected override IEnumerable<string> CheckCircularDependenciesForPackages => Enumerable.Empty<string>();
            protected override bool IsWIP => true;
        }
    }
}